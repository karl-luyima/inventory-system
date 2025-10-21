<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryClerk;
use App\Models\Kpi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class InventoryClerkController extends Controller
{
    // ================= Dashboard =================
    public function dashboard()
    {
        $products = Product::all();
        return view('inventory.dashboard', compact('products'));
    }

    // ================= Search Products =================
    public function search(Request $request)
    {
        $products = Product::where('pdt_name', 'like', '%' . $request->search . '%')
            ->orWhere('pdt_id', 'like', '%' . $request->search . '%')
            ->get();

        return view('inventory.dashboard', compact('products'));
    }

    // ================= Update Stock =================
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->stock_level = $request->stock;
        $product->save();

        return redirect()->route('clerk.dashboard')->with('success', 'Stock updated successfully.');
    }

    // ================= Save Sale (New) =================
    public function saveSale(Request $request)
    {
        $request->validate([
            'pdt_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_level' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Ensure at least one inventory exists
            $inventory = Inventory::first();

            if (!$inventory) {
                $inventory = Inventory::create([
                    'inventory_name' => 'Default Inventory',
                    'description' => 'Auto-created by system for first product entry',
                ]);
            }

            // Create product linked to this inventory
            Product::create([
                'pdt_name' => $request->pdt_name,
                'price' => $request->price,
                'stock_level' => $request->stock_level,
                'inventory_id' => $inventory->inventory_id,
            ]);

            DB::commit();

            return redirect()->route('clerk.dashboard')->with('success', 'Sale (product) saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '❌ Error saving sale: ' . $e->getMessage());
        }
    }

    // ================= Create Inventory Clerk =================
    public function createClerk(Request $request)
    {
        $request->validate([
            'clerk_name' => 'required|string|max:255',
            'clerk_email' => 'required|email|unique:inventory_clerks,clerk_email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        InventoryClerk::create([
            'clerk_name' => $request->clerk_name,
            'clerk_email' => $request->clerk_email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Inventory clerk created successfully.');
    }

    // ================= View Dashboard Metrics =================
    public function metrics()
    {
        $kpis = Kpi::all();
        return view('inventory.metrics', compact('kpis'));
    }

    // ================= Redirect to Dashboard =================
    public function index()
    {
        return redirect()->route('clerk.dashboard');
    }
}
