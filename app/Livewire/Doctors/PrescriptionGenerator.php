<?php

namespace App\Livewire\Doctors;

use Livewire\Component;

class PrescriptionGenerator extends Component
{
    public $patient_name = '';
    public $patient_age = '';
    public $symptoms = '';
    public $diagnosis = '';
    public $medicines = [];

    public function mount()
    {
        $this->addMedicine();
    }

    public function addMedicine()
    {
        $this->medicines[] = [
            'name' => '',
            'dosage' => '1+0+1',
            'duration' => '7 days',
            'instructions' => 'After meal'
        ];
    }

    public function removeMedicine($index)
    {
        unset($this->medicines[$index]);
        $this->medicines = array_values($this->medicines);
    }

    public function savePrescription()
    {
        session()->flash('message', 'Prescription generated & saved successfully!');
    }

    public function render()
    {
        return view('components.doctors.prescription-generator');
    }
}
