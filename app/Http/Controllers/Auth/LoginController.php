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
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        // Check Admin
        $admin = Administrator::where('admin_email', $email)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            Session::put('role', 'admin');
            Session::put('user_name', $admin->admin_name);
            return redirect()->route('admin.home');
        }

        // Check Inventory Clerk
        $clerk = InventoryClerk::where('clerk_email', $email)->first();
        if ($clerk && Hash::check($password, $clerk->password)) {
            Session::put('role', 'clerk');
            Session::put('user_name', $clerk->clerk_name);
            return redirect()->route('clerk.dashboard');
        }

        // Check Sales Analyst
        $analyst = SalesAnalyst::where('analyst_email', $email)->first();
        if ($analyst && Hash::check($password, $analyst->password)) {
            Session::put('role', 'analyst');
            Session::put('user_name', $analyst->analyst_name);
            return redirect()->route('analyst.dashboard');
        }

        return back()->withErrors(['login' => 'Invalid email or password.']);
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login');
    }
}
