<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class InventoryClerkController extends Controller
{
    // Show dashboard with all products
    public function dashboard()
    {
        $products = Product::all();
        return view('clerk.dashboard', compact('products'));
    }

    // Search products
    public function search(Request $request)
    {
        $products = Product::where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('pdt_id', 'like', '%' . $request->search . '%')
                            ->get();

        return view('clerk.dashboard', compact('products'));
    }

    // Update product stock
    public function updateStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->stock = $request->stock;
        $product->save();

        return redirect()->route('clerk.dashboard')->with('success', 'Stock updated successfully.');
    }
}
