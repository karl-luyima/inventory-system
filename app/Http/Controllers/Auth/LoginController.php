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
                $emailField = match ($role) {
                    'admin' => 'admin_email',
                    'clerk' => 'clerk_email',
                    'analyst' => 'analyst_email',
                };
                $query->where($emailField, $email);
            })->first();

            if ($user && Hash::check($password, $user->password)) {
                // Set session
                Session::put('role', $role);
                $nameField = match ($role) {
                    'admin' => 'admin_name',
                    'clerk' => 'clerk_name',
                    'analyst' => 'analyst_name',
                };
                Session::put('name', $user->{$nameField});

                // Redirect based on role
                $route = match ($role) {
                    'admin' => 'admin.dashboard',
                    'clerk' => 'clerk.dashboard',
                    'analyst' => 'sales.dashboard',
                };

                return redirect()->route($route);
            }
        }

        return back()->withErrors(['login' => 'Invalid email or password.'])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
