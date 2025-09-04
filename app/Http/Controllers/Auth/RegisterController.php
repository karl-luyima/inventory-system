<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Administrator;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;

class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function registerSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:administrator,inventory_clerk,sales_analyst'
        ]);

        $hashedPassword = Hash::make($request->password);

        switch ($request->role) {
            case 'administrator':
                Administrator::create([
                    'admin_name' => $request->name,
                    'admin_email' => $request->email,
                    'password' => $hashedPassword
                ]);
                break;

            case 'inventory_clerk':
                InventoryClerk::create([
                    'clerk_name' => $request->name,
                    'clerk_email' => $request->email,
                    'password' => $hashedPassword
                ]);
                break;

            case 'sales_analyst':
                SalesAnalyst::create([
                    'analyst_name' => $request->name,
                    'analyst_email' => $request->email,
                    'password' => $hashedPassword
                ]);
                break;
        }

        return redirect()->route('login')->with('success', 'Account created successfully!');
    }
}
