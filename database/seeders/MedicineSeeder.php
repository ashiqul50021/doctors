<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('medicine.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("medicine.csv file not found at: {$csvPath}");
            return;
        }

        $this->command->info("Starting medicine database seeding from medicine.csv...");

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file); // Skip header: Generic, Name, Company, Strength, Price

        $batch = [];
        $batchSize = 1000;
        $totalInserted = 0;
        $now = now()->toDateTimeString();

        DB::disableQueryLog();

        while (($row = fgetcsv($file)) !== false) {
            if (empty($row) || count($row) < 2) {
                continue;
            }

            $generic  = !empty($row[0]) ? trim($row[0]) : null;
            $name     = !empty($row[1]) ? trim($row[1]) : '';
            $company  = !empty($row[2]) ? trim($row[2]) : null;
            $strength = !empty($row[3]) ? trim($row[3]) : null;

            // Clean price (e.g. "1,079.47" -> 1079.47 or "N/A" -> null)
            $priceRaw = !empty($row[4]) ? trim($row[4]) : null;
            $price = null;
            if ($priceRaw && is_numeric(str_replace([',', ' '], '', $priceRaw))) {
                $price = (float) str_replace([',', ' '], '', $priceRaw);
            }

            if (empty($name)) {
                continue;
            }

            $batch[] = [
                'name'       => $name,
                'generic'    => $generic,
                'company'    => $company,
                'strength'   => $strength,
                'price'      => $price,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= $batchSize) {
                DB::table('medicines')->insert($batch);
                $totalInserted += count($batch);
                $batch = [];
                $this->command->info("Seeded {$totalInserted} medicines...");
            }
        }

        if (!empty($batch)) {
            DB::table('medicines')->insert($batch);
            $totalInserted += count($batch);
        }

        fclose($file);

        $this->command->info("Successfully seeded a total of {$totalInserted} medicines into database!");
    }
}
