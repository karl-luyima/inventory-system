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

        // Array of roles and their models
        $roles = [
            'admin' => Administrator::class,
            'clerk' => InventoryClerk::class,
            'analyst' => SalesAnalyst::class,
        ];

        foreach ($roles as $role => $model) {
            $user = $model::where(function ($query) use ($role, $email) {
                switch ($role) {
                    case 'admin':
                        $query->where('admin_email', $email);
                        break;
                    case 'clerk':
                        $query->where('clerk_email', $email);
                        break;
                    case 'analyst':
                        $query->where('analyst_email', $email);
                        break;
                }
            })->first();

            if ($user && Hash::check($password, $user->password)) {
                // Set session
                Session::put('role', $role);
                Session::put('user_name', $user->{$role . '_name'});
                // Redirect to role-specific dashboard
                $route = match ($role) {
                    'admin' => 'admin.home',
                    'clerk' => 'clerk.dashboard',
                    'analyst' => 'analyst.dashboard',
                };
                return redirect()->route($route);
            }
        }

        // Invalid credentials
        return back()->withErrors(['login' => 'Invalid email or password.'])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        // Clear all session data
        $request->session()->flush();

        // Regenerate CSRF token for security
        $request->session()->regenerateToken();

        // Redirect to login page
        return redirect()->route('login');
    }
}
