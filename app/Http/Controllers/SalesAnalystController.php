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
        $sales = Sale::with('product')->orderBy('created_at', 'desc')->take(10)->get();

        $topProducts = Product::withSum('sales', 'quantity')
            ->withSum('sales', 'totalAmount')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->map(fn($product) => (object)[
                'pdt_name' => $product->pdt_name,
                'total_sold' => $product->sales_sum_quantity ?? 0,
                'total_amount' => $product->{"sales_sum_total_amount"} ?? 0
            ]);

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
            $product = Product::firstOrCreate(
                ['pdt_name' => $request->pdt_name],
                [
                    'price' => $request->price ?? 0,
                    'stock_level' => $request->stock_level ?? 0,
                    'inventory_id' => $request->inventory_id,
                ]
            );

            if ($product->stock_level < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available for this sale.'
                ], 400);
            }

            $product->stock_level -= $request->quantity;
            $product->save();

            $sale = Sale::create([
                'pdt_id' => $product->pdt_id,
                'quantity' => $request->quantity,
                'totalAmount' => $request->totalAmount,
                'date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sale recorded successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ================= Reports =================
    public function reports()
    {
        $sales = Sale::with('product')->latest()->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('totalAmount');
        $totalProducts = $sales->sum('quantity');

        $topProducts = Product::withSum('sales', 'quantity')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->map(fn($product) => (object)[
                'pdt_name' => $product->pdt_name,
                'total_sold' => $product->sales_sum_quantity ?? 0
            ]);

        return view('sales.reports', compact('sales', 'totalSales', 'totalRevenue', 'totalProducts', 'topProducts'));
    }

    // ================= Download Report PDF =================
    public function downloadReport()
    {
        $sales = Sale::with('product')->latest()->get();

        $topProducts = Product::withSum('sales', 'quantity')
            ->withSum('sales', 'totalAmount')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->filter(fn($product) => $product->sales_sum_quantity > 0)
            ->map(fn($product) => (object)[
                'pdt_name' => $product->pdt_name,
                'total_sold' => $product->sales_sum_quantity ?? 0,
                'total_amount' => $product->{"sales_sum_total_amount"} ?? 0
            ]);

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

    // ================= Fetch Sales Data for JS =================
    public function fetchSalesData()
    {
        $sales = Sale::with('product')->orderBy('created_at', 'desc')->take(10)->get();

        $topProducts = Product::withSum('sales', 'quantity')
            ->withSum('sales', 'totalAmount')
            ->orderByDesc('sales_sum_quantity')
            ->take(5)
            ->get()
            ->map(fn($product) => (object)[
                'pdt_name' => $product->pdt_name,
                'total_sold' => $product->sales_sum_quantity ?? 0,
                'total_amount' => $product->{"sales_sum_total_amount"} ?? 0
            ]);

        return response()->json([
            'sales' => $sales,
            'topProducts' => $topProducts
        ]);
    }
}
