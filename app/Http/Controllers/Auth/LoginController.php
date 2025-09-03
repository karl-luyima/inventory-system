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
        ]);

        // Find user
        $user = User::where('user_id', $request->user_id)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Invalid credentials.']);
        }

        // Log the user in (session-based)
        Auth::login($user);

        // Detect role automatically
        if (Administrator::where('user_id', $user->user_id)->exists()) {
            return redirect()->route('admin.dashboard');
        }

        if (InventoryClerk::where('user_id', $user->user_id)->exists()) {
            return redirect()->route('clerk.dashboard');
        }

        if (SalesAnalyst::where('user_id', $user->user_id)->exists()) {
            return redirect()->route('analyst.dashboard');
        }

        // If no role found, logout
        Auth::logout();
        return back()->withErrors(['login' => 'No role assigned to this account.']);
    }
}
