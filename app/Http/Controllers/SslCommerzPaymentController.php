<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentBookedMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SslCommerzPaymentController extends Controller
{
    public function success(Request $request)
    {
        $appointment = $this->findAppointment($request->input('tran_id'));

        if (!$appointment) {
            return redirect()->route('home')->with('error', 'Appointment not found');
        }

        if ($appointment->status === 'pending') {
            // Never trust the browser POST alone: confirm the payment with SSLCommerz first.
            $validation = $this->validateWithGateway($request->input('val_id'), $appointment);
            if (!$validation) {
                return redirect()->route('home')->with('error', 'Payment could not be verified. Please contact support if money was deducted.');
            }

            $this->markPaid($appointment, $validation);

            return redirect()->route('booking.success')
                ->with($this->flashData($appointment))
                ->with('success', 'Payment successful and appointment confirmed.');
        }

        if ($appointment->status === 'confirmed') {
            return redirect()->route('booking.success')
                ->with($this->flashData($appointment))
                ->with('success', 'Payment already verified.');
        }

        return redirect()->route('home')->with('error', 'Invalid Appointment Status');
    }

    public function fail(Request $request)
    {
        $this->cancelPending($request->input('tran_id'), 'failed');

        return redirect()->route('home')->with('error', 'Payment Failed');
    }

    public function cancel(Request $request)
    {
        $this->cancelPending($request->input('tran_id'), 'cancelled');

        return redirect()->route('home')->with('error', 'Payment Cancelled');
    }

    public function ipn(Request $request)
    {
        $appointment = $this->findAppointment($request->input('tran_id'));

        if ($appointment && $appointment->status === 'pending') {
            $validation = $this->validateWithGateway($request->input('val_id'), $appointment);
            if ($validation) {
                $this->markPaid($appointment, $validation);
            }
        }

        return response()->json(['message' => 'IPN received']);
    }

    private function findAppointment(?string $tranId): ?Appointment
    {
        if (!$tranId || !str_starts_with($tranId, 'APT-')) {
            return null;
        }

        return Appointment::with(['patient.user', 'doctor.user'])->find((int) substr($tranId, 4));
    }

    /**
     * Ask SSLCommerz whether this val_id is a real, completed payment for this appointment.
     * Returns the gateway response on success, null otherwise.
     */
    private function validateWithGateway(?string $valId, Appointment $appointment): ?array
    {
        if (!$valId || $appointment->payment_method !== 'sslcommerz') {
            return null;
        }

        $storeId = SiteSetting::get('sslcz_store_id', env('SSLCZ_STORE_ID', 'testbox'));
        $storePassword = SiteSetting::get('sslcz_store_password', env('SSLCZ_STORE_PASSWORD', 'qwerty'));
        $isTestMode = SiteSetting::get('sslcz_testmode', '1') == '1';

        $url = $isTestMode
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';

        try {
            $result = Http::timeout(20)->get($url, [
                'val_id' => $valId,
                'store_id' => $storeId,
                'store_passwd' => $storePassword,
                'format' => 'json',
            ])->json();
        } catch (\Throwable $e) {
            Log::error('SSLCommerz validation request failed: ' . $e->getMessage());
            return null;
        }

        $valid = is_array($result)
            && in_array($result['status'] ?? '', ['VALID', 'VALIDATED'], true)
            && ($result['tran_id'] ?? '') === 'APT-' . $appointment->id
            && ($result['currency_type'] ?? 'BDT') === 'BDT'
            && abs((float) ($result['currency_amount'] ?? $result['amount'] ?? 0) - (float) $appointment->fee) < 0.01;

        if (!$valid) {
            Log::warning('SSLCommerz validation rejected for appointment ' . $appointment->id, ['response' => $result]);
            return null;
        }

        return $result;
    }

    private function markPaid(Appointment $appointment, array $validation): void
    {
        $appointment->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'transaction_id' => $validation['bank_tran_id'] ?? ($validation['val_id'] ?? $appointment->transaction_id),
        ]);

        if ($appointment->patient && $appointment->patient->user && filter_var($appointment->patient->user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($appointment->patient->user->email)->send(new AppointmentBookedMail($appointment));
            } catch (\Throwable $e) {
                Log::warning('Appointment email could not be sent: ' . $e->getMessage());
            }
        }
    }

    /** Only cancel appointments that are still waiting for an SSLCommerz payment. */
    private function cancelPending(?string $tranId, string $paymentStatus): void
    {
        $appointment = $this->findAppointment($tranId);

        if ($appointment && $appointment->status === 'pending' && $appointment->payment_method === 'sslcommerz') {
            $appointment->update(['status' => 'cancelled', 'payment_status' => $paymentStatus]);
        }
    }

    private function flashData(Appointment $appointment): array
    {
        $doctor = Doctor::with('user')->find($appointment->doctor_id);

        return [
            'appointment_id' => $appointment->id,
            'meeting_link' => $appointment->meeting_link,
            'token_number' => $appointment->token_number,
            'type' => $appointment->type,
            'doctor_name' => $doctor->user->name ?? 'Doctor',
            'doctor_user_id' => $doctor ? $doctor->user_id : null,
            'date' => $appointment->appointment_date,
            'time' => $appointment->appointment_time,
            'payment_method' => $appointment->payment_method,
            'payment_status' => $appointment->payment_status,
            'transaction_id' => $appointment->transaction_id,
        ];
    }
}
