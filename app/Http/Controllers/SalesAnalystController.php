<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;

class SalesAnalystController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $sales = Sale::latest()->take(5)->get();
        $topProducts = Product::orderBy('stock', 'desc')->take(5)->get();
        return view('analyst.dashboard', compact('sales', 'topProducts'));
    }

    // Record new sale
    public function store(Request $request)
    {
        $request->validate([
            'pdt_id' => 'required|exists:products,pdt_id',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
        ]);

        // Save sale
        $sale = new Sale();
        $sale->pdt_id = $request->pdt_id;
        $sale->quantity = $request->quantity;
        $sale->amount = $request->amount;
        $sale->save();

        // Decrease product stock
        $product = Product::find($request->pdt_id);
        $product->stock -= $request->quantity;
        $product->save();

        return redirect()->route('analyst.dashboard')->with('success', 'Sale recorded successfully.');
    }
}
