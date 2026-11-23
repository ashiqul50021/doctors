<?php

namespace App\Http\Controllers;

use App\Models\BuyerProfile;
use App\Models\SupplierProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Modules\Ecommerce\Models\SellerProfile;

class SignupController extends Controller
{
    /** Roles that have their own legacy form are linked directly from the chooser. */
    public static function roleCards(): array
    {
        return [
            ['key' => 'doctor', 'title' => 'Doctor', 'icon' => 'fa-user-md', 'desc' => 'Offer consultations. Admin approval required.', 'url' => route('doctor.register')],
            ['key' => 'patient', 'title' => 'Patient', 'icon' => 'fa-procedures', 'desc' => 'Book doctors and manage appointments.', 'url' => route('signup.patient')],
            ['key' => 'buyer', 'title' => 'Buyer', 'icon' => 'fa-shopping-bag', 'desc' => 'Buy medicines and health products.', 'url' => route('signup.form', 'buyer')],
            ['key' => 'seller', 'title' => 'Seller', 'icon' => 'fa-store', 'desc' => 'Sell products from your own store. Admin approval required.', 'url' => route('signup.form', 'seller')],
            ['key' => 'agent', 'title' => 'Agent', 'icon' => 'fa-handshake', 'desc' => 'Earn commission on bookings and sales. Admin approval required.', 'url' => route('agent.register')],
            ['key' => 'supplier', 'title' => 'Supplier', 'icon' => 'fa-truck', 'desc' => 'Wholesale supplier. Admin approval required.', 'url' => route('signup.form', 'supplier')],
        ];
    }

    private function formConfig(string $role): array
    {
        $configs = [
            'buyer' => [
                'title' => 'Buyer Sign Up',
                'subtitle' => 'Create an account to order products',
                'fields' => [
                    ['name' => 'phone', 'label' => 'Mobile Number', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Delivery Address', 'type' => 'textarea', 'required' => false],
                ],
            ],
            'seller' => [
                'title' => 'Seller Sign Up',
                'subtitle' => 'Open your store. Admin will review your account before you can sell.',
                'fields' => [
                    ['name' => 'store_name', 'label' => 'Store Name', 'type' => 'text', 'required' => true],
                    ['name' => 'phone', 'label' => 'Mobile Number', 'type' => 'text', 'required' => true],
                    ['name' => 'address', 'label' => 'Store Address', 'type' => 'textarea', 'required' => false],
                ],
            ],
            'supplier' => [
                'title' => 'Supplier Sign Up',
                'subtitle' => 'Register as a wholesale supplier. Admin will review your account.',
                'fields' => [
                    ['name' => 'company_name', 'label' => 'Company Name', 'type' => 'text', 'required' => true],
                    ['name' => 'phone', 'label' => 'Mobile Number', 'type' => 'text', 'required' => true],
                    ['name' => 'trade_license_no', 'label' => 'Trade License No.', 'type' => 'text', 'required' => false],
                    ['name' => 'address', 'label' => 'Company Address', 'type' => 'textarea', 'required' => false],
                    ['name' => 'products_description', 'label' => 'What do you supply?', 'type' => 'textarea', 'required' => false],
                ],
            ],
        ];

        abort_unless(isset($configs[$role]), 404);

        return $configs[$role];
    }

    public function choose()
    {
        if (Auth::check()) {
            return app(AuthController::class)->redirectUser(Auth::user());
        }

        return view('frontend.signup.choose', ['cards' => self::roleCards()]);
    }

    public function showForm(string $role)
    {
        if (Auth::check()) {
            return app(AuthController::class)->redirectUser(Auth::user());
        }

        return view('frontend.signup.form', [
            'role' => $role,
            'config' => $this->formConfig($role),
        ]);
    }

    public function submit(Request $request, string $role)
    {
        $this->formConfig($role); // 404 for unknown roles

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
        ];

        if ($role === 'seller') {
            $rules['store_name'] = ['required', 'string', 'max:255', 'unique:seller_profiles,store_name'];
        }
        if ($role === 'supplier') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['trade_license_no'] = ['nullable', 'string', 'max:100'];
            $rules['products_description'] = ['nullable', 'string', 'max:2000'];
        }

        $request->validate($rules);

        $user = DB::transaction(function () use ($request, $role) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role,
            ]);

            if ($role === 'buyer') {
                BuyerProfile::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            } elseif ($role === 'seller') {
                $base = Str::slug($request->store_name) ?: 'store';
                $slug = $base;
                $i = 1;
                while (SellerProfile::where('store_slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                SellerProfile::create([
                    'user_id' => $user->id,
                    'store_name' => $request->store_name,
                    'store_slug' => $slug,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'status' => 'pending',
                    'commission_rate' => 0,
                ]);
            } else {
                SupplierProfile::create([
                    'user_id' => $user->id,
                    'company_name' => $request->company_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'trade_license_no' => $request->trade_license_no,
                    'products_description' => $request->products_description,
                    'status' => 'pending',
                ]);
            }

            return $user;
        });

        // Buyers can use the site immediately; the other roles wait for admin approval.
        if ($role === 'buyer') {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect('/')->with('success', 'Welcome! Your account has been created.');
        }

        return redirect()->route('login')->with('success', 'Registration submitted. You can log in after admin approval.');
    }
}
