<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class InventoryClerkController extends Controller
{
    // Inventory Clerk Dashboard
    public function dashboard()
    {
        return view('inventory.dashboard', [
            'totalProducts'=> Product::count(),
            'totalStock'   => Product::sum('stock'),
            'lowStock'     => Product::where('stock', '<', 10)->get(),

            // Chart data
            'productNames' => Product::pluck('name'),
            'productStock' => Product::pluck('stock'),
        ]);
    }
}
