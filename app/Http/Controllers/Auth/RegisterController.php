<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Administrator;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Show registration form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration
    public function registerSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'role' => 'required'
        ]);

        // Create new user
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        // Insert into respective role table
        switch ($request->role) {
            case 'administrator':
                Administrator::create([
                    'user_id' => $user->user_id,
                    'admin_email' => $request->email,
                    'admin_name' => $request->name
                ]);
                break;

            case 'inventory_clerk':
                InventoryClerk::create([
                    'user_id' => $user->user_id,
                    'clerk_email' => $request->email,
                    'clerk_name' => $request->name
                ]);
                break;

            case 'sales_analyst':
                SalesAnalyst::create([
                    'user_id' => $user->user_id,
                    'analyst_email' => $request->email,
                    'analyst_name' => $request->name
                ]);
                break;
        }

        return redirect()->route('login')->with('success', 'Account created successfully!');
    }
}
