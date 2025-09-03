<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Administrator;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'password' => 'required',
            'role' => 'required|in:administrator,inventory_clerk,sales_analyst',
        ]);

        // Find user
        $user = User::where('user_id', $request->user_id)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Invalid credentials.']);
        }

        // Store user in session (manual auth since we use custom user_id)
        Auth::login($user);

        // Redirect based on role
        switch ($request->role) {
            case 'administrator':
                if (Administrator::where('user_id', $user->user_id)->exists()) {
                    return redirect()->route('admin.dashboard');
                }
                break;

            case 'inventory_clerk':
                if (InventoryClerk::where('user_id', $user->user_id)->exists()) {
                    return redirect()->route('clerk.dashboard');
                }
                break;

            case 'sales_analyst':
                if (SalesAnalyst::where('user_id', $user->user_id)->exists()) {
                    return redirect()->route('analyst.dashboard');
                }
                break;
        }

        // If mismatch between role & DB
        Auth::logout();
        return back()->withErrors(['login' => 'Role does not match your account.']);
    }
}
