<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Administrator;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'user_id' => 'required|unique:users,user_id',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:administrator,inventory_clerk,sales_analyst',
        ]);

        // Create user
        $user = User::create([
            'user_id' => $request->user_id,
            'password' => Hash::make($request->password),
        ]);

        // Assign role
        switch ($request->role) {
            case 'administrator':
                Administrator::create([
                    'user_id' => $user->user_id,
                    'admin_email' => null,
                    'admin_name' => null,
                ]);
                break;

            case 'inventory_clerk':
                InventoryClerk::create([
                    'user_id' => $user->user_id,
                    'clerk_email' => null,
                    'clerk_name' => null,
                ]);
                break;

            case 'sales_analyst':
                SalesAnalyst::create([
                    'user_id' => $user->user_id,
                    'analyst_email' => null,
                    'analyst_name' => null,
                ]);
                break;
        }

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }
}
