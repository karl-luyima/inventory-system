<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryClerk;
use App\Models\Kpi;
use App\Models\Sale;
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

    // ================= Save Sale =================
    public function saveSale(Request $request)
    {
        $request->validate([
            'pdt_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'inventory_id' => 'required|exists:inventories,inventory_id',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Use inventory selected in the form
            $inventory_id = $request->inventory_id;

            // ✅ Find or create product under the selected inventory
            $product = Product::firstOrCreate(
                ['pdt_name' => $request->pdt_name, 'inventory_id' => $inventory_id],
                [
                    'price' => $request->price,
                    'stock_level' => 0,
                ]
            );

            // ✅ Check stock availability
            if ($product->stock_level < $request->quantity) {
                return redirect()->back()->with('error', 'Not enough stock available for this sale.');
            }

            // ✅ Reduce stock
            $product->stock_level -= $request->quantity;
            $product->save();

            // ✅ Record sale in sales table
            Sale::create([
                'pdt_id' => $product->pdt_id,
                'quantity' => $request->quantity,
                'totalAmount' => $request->quantity * $product->price,
                'date' => now()->toDateString(),
            ]);

            DB::commit();

            return redirect()->route('clerk.dashboard')->with('success', 'Sale saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error saving sale: ' . $e->getMessage());
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

    // ================= Show Create Product Form =================
    public function createProduct()
    {
        $inventories = Inventory::all(); // For selecting which inventory the product belongs to
        return view('inventory.product-form', compact('inventories'));
    }

    // ================= Store Product =================
    public function storeProduct(Request $request)
    {
        $request->validate([
            'pdt_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_level' => 'required|integer|min:0',
            'inventory_id' => 'required|exists:inventories,inventory_id',
        ]);

        Product::create([
            'pdt_name' => $request->pdt_name,
            'price' => $request->price,
            'stock_level' => $request->stock_level,
            'inventory_id' => $request->inventory_id,
        ]);

        return redirect()->route('clerk.dashboard')->with('success', 'Product created successfully!');
    }

    // ================= Redirect to Dashboard =================
    public function index()
    {
        return redirect()->route('clerk.dashboard');
    }

    // ================= Show Create Inventory Form =================
    public function createInventory()
    {
        return view('inventory.inventory-form');
    }

    // ================= Store Inventory =================
    public function storeInventory(Request $request)
    {
        $request->validate([
            'inventory_name' => 'required|string|max:255',
            'pdtList' => 'nullable|string',
        ]);

        Inventory::create([
            'inventory_name' => $request->inventory_name,
            'pdtList' => $request->pdtList ?? '[]',
        ]);

        return redirect()->route('clerk.dashboard')->with('success', 'Inventory created successfully!');
    }
}
