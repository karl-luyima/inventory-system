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

        // Map roles to their models and name columns
        $roles = [
            'administrator' => ['model' => Administrator::class, 'name_column' => 'admin_name', 'email_column' => 'admin_email'],
            'inventory_clerk' => ['model' => InventoryClerk::class, 'name_column' => 'clerk_name', 'email_column' => 'clerk_email'],
            'sales_analyst' => ['model' => SalesAnalyst::class, 'name_column' => 'analyst_name', 'email_column' => 'analyst_email'],
        ];

        $roleData = $roles[$request->role];
        $model = $roleData['model'];

        $model::create([
            $roleData['name_column'] => $request->name,
            $roleData['email_column'] => $request->email,
            'password' => $hashedPassword,
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully!');
    }
}
