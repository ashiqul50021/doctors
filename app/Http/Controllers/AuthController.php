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
            if (Auth::user()->role === 'doctor') {
                return $this->redirectDoctorAfterAuth(Auth::user()->doctor);
            }
            return redirect()->route('patient.dashboard');
        }
        return view('frontend.login');
    }

    // Handle Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Block admin accounts from normal/patient login form.
            if ($user->role === 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')->withErrors([
                    'email' => 'Admin account detected. Please use the admin login page.',
                ])->onlyInput('email');
            }

            // Redirect based on role
            if ($user->role === 'doctor') {
                return $this->redirectDoctorAfterAuth($user->doctor);
            }

            return redirect()->route('patient.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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
            if (Auth::user()->role === 'doctor') {
                return $this->redirectDoctorAfterAuth(Auth::user()->doctor);
            }
            return redirect()->route('patient.dashboard');
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
            if (Auth::user()->role === 'doctor') {
                return $this->redirectDoctorAfterAuth(Auth::user()->doctor);
            }
            return redirect()->route('patient.dashboard');
        }
        return view('frontend.doctor-register');
    }

    // Handle Doctor Registration
    public function registerDoctor(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms_accept' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
        ]);

        // Create Doctor Profile (Pending Status)
        Doctor::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'phone' => $request->mobile, // Assuming phone field exists in doctor table from migration
            // Speciality and Qualification are now nullable
        ]);

        Auth::login($user);

        return redirect()->route('doctors.profile.settings')
            ->with('warning', 'Please complete your profile to activate your doctor account.');
    }

    private function redirectDoctorAfterAuth(?Doctor $doctor): RedirectResponse
    {
        if (!$doctor || !$doctor->isProfileComplete()) {
            return redirect()->route('doctors.profile.settings')
                ->with('warning', 'Please complete your profile to activate your doctor account.');
        }

        if ($doctor->status !== 'approved') {
            $doctor->update(['status' => 'approved']);
        }

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
