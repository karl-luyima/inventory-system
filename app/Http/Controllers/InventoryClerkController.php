<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryClerk;
use Illuminate\Support\Facades\Hash;

class InventoryClerkController extends Controller
{
    // ================= Dashboard =================
    public function dashboard()
    {
        $products = Product::all();
        return view('clerk.dashboard', compact('products'));
    }

    // ================= Search Products =================
    public function search(Request $request)
    {
        $products = Product::where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('pdt_id', 'like', '%' . $request->search . '%')
                            ->get();

        return view('clerk.dashboard', compact('products'));
    }

    // ================= Update Stock =================
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->stock = $request->stock;
        $product->save();

        return redirect()->route('clerk.dashboard')->with('success', 'Stock updated successfully.');
    }

    // ================= Create Inventory Clerk =================
    public function createClerk(Request $request)
    {
        $request->validate([
            'clerk_name' => 'required|string|max:255',
            'clerk_email' => 'required|email|unique:inventory_clerks,clerk_email',
            'password' => 'required|string|min:6|confirmed', // expects password_confirmation
        ]);

        InventoryClerk::create([
            'clerk_name' => $request->clerk_name,
            'clerk_email' => $request->clerk_email,
            'password' => Hash::make($request->password), // hash password
        ]);

        return redirect()->back()->with('success', 'Inventory clerk created successfully.');
    }

    // ================= Redirect to Dashboard =================
    public function index()
    {
        return redirect()->route('clerk.dashboard');
    }
}
