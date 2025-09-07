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
                Session::put('name', $user->{$role . '_name'}); // ✅ changed to "name"

                // Redirect
                $route = match ($role) {
                    'admin' => 'admin.home',
                    'clerk' => 'clerk.dashboard',
                    'analyst' => 'sales.dashboard', // ✅ match Blade
                };
                return redirect()->route($route);
            }
        }

        return back()->withErrors(['login' => 'Invalid email or password.'])->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
