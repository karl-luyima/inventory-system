<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Administrator;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;

class LoginController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login submission
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        // Updated to match registration roles
        $roles = [
            'administrator' => Administrator::class,
            'inventory_clerk' => InventoryClerk::class,
            'sales_analyst' => SalesAnalyst::class,
        ];

        foreach ($roles as $role => $model) {
            $emailField = match ($role) {
                'administrator' => 'admin_email',
                'inventory_clerk' => 'clerk_email',
                'sales_analyst' => 'analyst_email',
            };

            $nameField = match ($role) {
                'administrator' => 'admin_name',
                'inventory_clerk' => 'clerk_name',
                'sales_analyst' => 'analyst_name',
            };

            $dashboardRoute = match ($role) {
                'administrator' => 'admin.dashboard',
                'inventory_clerk' => 'clerk.dashboard',
                'sales_analyst' => 'sales.dashboard',
            };

            $user = $model::where($emailField, $email)->first();

            if ($user && Hash::check($password, $user->password)) {
                // Set session
                Session::put('role', $role);
                Session::put('name', $user->{$nameField});

                return redirect()->route($dashboardRoute);
            }
        }

        return back()->withErrors(['login' => 'Invalid email or password.'])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
