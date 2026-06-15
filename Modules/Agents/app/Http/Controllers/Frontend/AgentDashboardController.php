<?php

namespace Modules\Agents\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Order;
use Modules\Courses\Models\Enrollment;
use Modules\Agents\Models\AgentTransaction;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $agent = Auth::user()->agent;
        
        $availableBalance = $agent->wallet_balance;
        
        $totalEarned = AgentTransaction::where('agent_id', $agent->id)
            ->whereIn('type', ['commission_booking', 'commission_product', 'commission_course'])
            ->sum('amount');
            
        $bookingsCount = Appointment::where('agent_id', $agent->id)->count();
        $salesCount = Order::where('agent_id', $agent->id)->count();
        $coursesCount = Enrollment::where('agent_id', $agent->id)->count();

        $recentBookings = Appointment::with('doctor.user', 'patient.user')
            ->where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = AgentTransaction::where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get();

        return view('agents::frontend.dashboard', compact(
            'agent',
            'availableBalance',
            'totalEarned',
            'bookingsCount',
            'salesCount',
            'coursesCount',
            'recentBookings',
            'recentOrders',
            'recentTransactions'
        ));
    }

    public function wallet()
    {
        $agent = Auth::user()->agent;
        $transactions = AgentTransaction::where('agent_id', $agent->id)
            ->latest()
            ->paginate(10);
            
        return view('agents::frontend.wallet', compact('agent', 'transactions'));
    }

    public function payoutRequest(Request $request)
    {
        $agent = Auth::user()->agent;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:500'],
            'payment_method' => ['required', 'string'],
            'account_number' => ['required', 'string'],
        ]);

        $amount = $request->amount;

        if ($agent->wallet_balance < $amount) {
            return back()->with('error', 'You do not have enough wallet balance to request this payout.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($agent, $amount, $request) {
            // Deduct immediately (lock balance)
            $agent->decrement('wallet_balance', $amount);

            // Create transaction of type payout_request (pending)
            AgentTransaction::create([
                'agent_id' => $agent->id,
                'type' => 'payout_request',
                'amount' => $amount,
                'description' => 'Payout request via ' . $request->payment_method . ' to ' . $request->account_number,
                'status' => 'pending',
            ]);
        });

        return back()->with('success', 'Payout request submitted successfully. Please wait for admin approval.');
    }

    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => ['required', 'image', 'max:10240'], // max 10MB
        ]);

        $agent = auth()->user()->agent;
        $file = $request->file('profile_image');

        try {
            // Load image using GD
            $imageInfo = getimagesize($file);
            $mime = $imageInfo['mime'];

            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = imagecreatefromjpeg($file);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file);
                    imagepalettetotruecolor($image);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file);
                    imagepalettetotruecolor($image);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($file);
                    break;
                default:
                    $image = imagecreatefromstring(file_get_contents($file));
            }

            if (!$image) {
                return response()->json(['error' => 'Invalid image file.'], 400);
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);
            $maxDim = 400; // max dimension for profile photos

            if ($width > $maxDim || $height > $maxDim) {
                $ratio = $width / $height;
                if ($ratio > 1) {
                    $newWidth = $maxDim;
                    $newHeight = (int) round($maxDim / $ratio);
                } else {
                    $newHeight = $maxDim;
                    $newWidth = (int) round($maxDim * $ratio);
                }

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                
                // Preserve transparency for PNGs
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);

                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resizedImage;
            }

            // Create directory if not exists
            $destinationPath = public_path('uploads/agents');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $filename = 'agent_' . $agent->id . '_' . time() . '.webp';
            $filePath = $destinationPath . '/' . $filename;

            // Compress and save as WebP (quality 80)
            imagewebp($image, $filePath, 80);
            imagedestroy($image);

            // Delete old image if exists
            if ($agent->profile_image && file_exists(public_path($agent->profile_image))) {
                @unlink(public_path($agent->profile_image));
            }

            // Update database
            $dbPath = 'uploads/agents/' . $filename;
            $agent->update([
                'profile_image' => $dbPath
            ]);

            return response()->json([
                'success' => true,
                'image_url' => asset($dbPath)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process image: ' . $e->getMessage()], 500);
        }
    }
}
