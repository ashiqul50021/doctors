<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    // Show Patient Login (Default)
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectAuthenticatedUser(Auth::user());
        }
        return view('frontend.login');
    }

    // Handle Login (Email or Mobile Number)
    public function login(Request $request)
    {
        $loginInput = trim($request->input('login', $request->input('email', '')));
        $password = $request->input('password');

        if (empty($loginInput) || empty($password)) {
            return back()->withErrors([
                'login' => 'Email or Mobile Number and Password are required.',
            ])->withInput();
        }

        $user = null;

        // 1. If it's a valid email, look up by email
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)->first();
        } else {
            // 2. Otherwise treat as phone number
            $cleanPhone = preg_replace('/[^0-9]/', '', $loginInput);
            if (!empty($cleanPhone)) {
                $last10 = substr($cleanPhone, -10);
                $patient = Patient::where('phone', $cleanPhone)
                    ->orWhere('phone', 'like', '%' . $last10)
                    ->latest()->first();

                if ($patient && $patient->user) {
                    $user = $patient->user;
                }

                // Fallback: check Doctor phone
                if (!$user) {
                    $doctor = Doctor::where('phone', $cleanPhone)
                        ->orWhere('phone', 'like', '%' . $last10)
                        ->latest()->first();
                    if ($doctor && $doctor->user) {
                        $user = $doctor->user;
                    }
                }

                // Fallback: check buyer / seller / supplier / agent phones
                if (!$user) {
                    $profileModels = [
                        \App\Models\BuyerProfile::class,
                        \Modules\Ecommerce\Models\SellerProfile::class,
                        \App\Models\SupplierProfile::class,
                        \Modules\Agents\Models\Agent::class,
                    ];
                    foreach ($profileModels as $model) {
                        $profile = $model::where('phone', $cleanPhone)
                            ->orWhere('phone', 'like', '%' . $last10)
                            ->latest()->first();
                        if ($profile && $profile->user) {
                            $user = $profile->user;
                            break;
                        }
                    }
                }

                // Fallback: check User auto-generated email
                if (!$user) {
                    $user = User::where('email', 'like', 'patient_' . $cleanPhone . '%')
                        ->orWhere('email', 'like', 'patient_%' . $last10 . '%')
                        ->first();
                }
            }
        }

        if ($user && Hash::check($password, $user->password)) {
            // Block admin accounts from normal/patient login form.
            if ($user->role === 'admin') {
                return redirect()->route('admin.login')->withErrors([
                    'email' => 'Admin account detected. Please use the admin login page.',
                ]);
            }

            // Roles that need admin approval cannot log in until approved.
            if ($blocked = $this->approvalBlockMessage($user)) {
                return back()->withErrors(['login' => $blocked])->withInput();
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            // Redirect based on role
            return $this->redirectUser($user);
        }

        return back()->withErrors([
            'login' => 'প্রদত্ত ইমেইল/মোবাইল নম্বর অথবা পাসওয়ার্ড সঠিক নয়।',
        ])->withInput();
    }

    // Show Doctor Login
    public function showDoctorLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'doctor') {
            return $this->redirectDoctorAfterAuth(Auth::user()->doctor);
        }
        return view('frontend.doctor-login');
    }

    // Handle Doctor Login
    public function doctorLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user is a doctor
            if ($user->role !== 'doctor') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'This account is not registered as a doctor. Please use the patient login instead.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return $this->redirectDoctorAfterAuth($user->doctor);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Show Patient Register
    public function showPatientRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectAuthenticatedUser(Auth::user());
        }
        return view('frontend.register');
    }

    // Handle Patient Registration
    public function registerPatient(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:15'], // Add mobile to User model if needed or create separate field
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'patient',
        ]);

        // Create Patient Profile
        Patient::create([
            'user_id' => $user->id,
            'phone' => $request->mobile,
            'address' => '', // Placeholder
            // Add other default fields if necessary
        ]);

        Auth::login($user);

        return redirect()->route('patient.dashboard');
    }

    // Show Doctor Register
    public function showDoctorRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectAuthenticatedUser(Auth::user());
        }
        return view('frontend.doctor-register');
    }

    private function redirectAuthenticatedUser(User $user): RedirectResponse
    {
        return $this->redirectUser($user);
    }

    /**
     * Message explaining why this user cannot log in yet, or null if allowed.
     * Doctors are not blocked here: they get the profile/pending page instead.
     */
    private function approvalBlockMessage(User $user): ?string
    {
        switch ($user->role) {
            case 'seller':
                $status = $user->sellerProfile->status ?? null;
                if ($status === 'pending') {
                    return 'Your seller account is pending admin approval.';
                }
                if ($status === 'suspended') {
                    return 'Your seller account has been suspended.';
                }
                return $status ? null : 'Seller profile not found for this account.';
            case 'agent':
                $status = $user->agent->status ?? null;
                if ($status === 'pending') {
                    return 'Your agent account is pending admin approval.';
                }
                if ($status === 'suspended') {
                    return 'Your agent account has been suspended.';
                }
                return $status ? null : 'Agent profile not found for this account.';
            case 'supplier':
                $status = $user->supplierProfile->status ?? null;
                if ($status === 'pending') {
                    return 'Your supplier account is pending admin approval.';
                }
                if ($status === 'rejected') {
                    return 'Your supplier application was rejected.';
                }
                if ($status === 'suspended') {
                    return 'Your supplier account has been suspended.';
                }
                return $status ? null : 'Supplier profile not found for this account.';
        }

        return null;
    }

    public function redirectUser(User $user): RedirectResponse
    {
        switch ($user->role) {
            case 'doctor':
                return $this->redirectDoctorAfterAuth($user->doctor);
            case 'seller':
                return redirect()->route('ecommerce.seller.dashboard');
            case 'agent':
                return redirect()->route('agent.dashboard');
            case 'supplier':
                return redirect()->route('supplier.dashboard');
            case 'buyer':
                return redirect('/');
            case 'admin':
                return redirect()->route('admin.dashboard');
        }

        return redirect()->route('patient.dashboard');
    }

    // Handle Doctor Registration
    public function registerDoctor(Request $request)
    {
        $cleanMobile = preg_replace('/[^0-9]/', '', (string)$request->mobile);
        if (str_starts_with($cleanMobile, '880')) {
            $cleanMobile = substr($cleanMobile, 3);
        }
        if (!str_starts_with($cleanMobile, '0') && strlen($cleanMobile) === 10) {
            $cleanMobile = '0' . $cleanMobile;
        }
        $request->merge(['mobile' => $cleanMobile]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^(01)[3-9][0-9]{8}$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms_accept' => ['accepted'],
        ], [
            'mobile.regex' => 'অনুগ্রহ করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন (যেমন: 017XXXXXXXX)।',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
        ]);

        // Create Doctor Profile (Pending Status)
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'phone' => $cleanMobile,
            // Speciality and Qualification are now nullable
        ]);

        Auth::login($user);

        return $this->redirectDoctorAfterAuth($doctor);
    }

    private function redirectDoctorAfterAuth(?Doctor $doctor): RedirectResponse
    {
        if (!$doctor) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Doctor profile not found.');
        }

        // 1. If profile is not complete, redirect to settings page
        if (!$doctor->isProfileComplete()) {
            return redirect()->route('doctors.profile.settings')
                ->with('warning', 'Please complete your profile to submit for admin approval.');
        }

        // 2. If profile is complete but status is not approved, redirect to settings page with pending notice
        if ($doctor->status !== 'approved') {
            return redirect()->route('doctors.profile.settings')
                ->with('info', 'Your profile is complete and pending admin approval. You will gain access to the dashboard once approved.');
        }

        // 3. Otherwise, go to dashboard
        return redirect()->route('doctors.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
