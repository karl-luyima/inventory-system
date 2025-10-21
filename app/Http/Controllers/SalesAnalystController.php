<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesAnalystController extends Controller
{
    // ================= Dashboard =================
    public function dashboard()
    {
        // Latest 10 sales with product info
        $sales = Sale::with('product')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Top 5 products by total quantity sold
        $topProducts = Product::withSum('sales', 'quantity')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->filter(fn($product) => $product->sales_sum_quantity > 0);

        return view('sales.dashboard', compact('sales', 'topProducts'));
    }

    // ================= Record a Sale =================
    public function store(Request $request)
    {
        $request->validate([
            'pdt_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'totalAmount' => 'required|numeric|min:0',
            'inventory_id' => 'required|exists:inventories,inventory_id',
        ]);

        try {
            // ✅ Find or create product under selected inventory
            $product = Product::firstOrCreate(
                ['pdt_name' => $request->pdt_name],
                [
                    'price' => $request->price ?? 0,
                    'stock_level' => 0,
                    'inventory_id' => $request->inventory_id,
                ]
            );

            // ✅ Check stock availability
            if ($product->stock_level < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available for this sale.'
                ], 400);
            }

            // ✅ Reduce stock
            $product->stock_level -= $request->quantity;
            $product->save();

            // ✅ Record sale in sales table
            $sale = Sale::create([
                'pdt_id' => $product->pdt_id,
                'quantity' => $request->quantity,
                'totalAmount' => $request->totalAmount,
                'date' => now(),
            ]);

            // Refresh latest sales & top products
            $sales = Sale::with('product')->orderBy('created_at', 'desc')->take(10)->get();
            $topProducts = Product::withSum('sales', 'quantity')
                ->orderByDesc('sales_sum_quantity')
                ->take(5)
                ->get()
                ->filter(fn($product) => $product->sales_sum_quantity > 0);

            return response()->json([
                'success' => true,
                'message' => 'Sale recorded successfully!',
                'sales' => $sales,
                'topProducts' => $topProducts,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ================= Reports View =================
    public function reports()
    {
        $sales = Sale::with('product')->latest()->get();

        $topProducts = Product::withSum('sales', 'quantity')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->filter(fn($product) => $product->sales_sum_quantity > 0);

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('totalAmount');
        $totalProducts = $sales->sum('quantity');
        $generatedAt = now()->format('d M Y H:i');

        return view('sales.reports', compact(
            'sales',
            'topProducts',
            'totalSales',
            'totalRevenue',
            'totalProducts',
            'generatedAt'
        ));
    }

    // ================= Download Report PDF =================
    public function downloadReport()
    {
        $sales = Sale::with('product')->latest()->get();

        $topProducts = Product::withSum('sales', 'quantity')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->filter(fn($product) => $product->sales_sum_quantity > 0);

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('totalAmount');
        $totalProducts = $sales->sum('quantity');
        $generatedAt = now()->format('d M Y H:i');

        $pdf = Pdf::loadView('sales.report_pdf', compact(
            'sales',
            'topProducts',
            'totalSales',
            'totalRevenue',
            'totalProducts',
            'generatedAt'
        ));

        return $pdf->download('sales_report_' . now()->format('Ymd_His') . '.pdf');
    }
}
