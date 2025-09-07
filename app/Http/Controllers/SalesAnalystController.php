<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesAnalystController extends Controller
{
    // Dashboard view
    public function dashboard()
    {
        $sales = Sale::with('product')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $topProducts = Sale::selectRaw('pdt_id, SUM(quantity) as total_sold, SUM(totalAmount) as total_amount')
            ->groupBy('pdt_id')
            ->with('product')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('sales.dashboard', compact('sales', 'topProducts'));
    }

    // Store a new sale via AJAX
    public function store(Request $request)
    {
        $request->validate([
            'pdt_id' => 'required|exists:products,pdt_id',
            'quantity' => 'required|numeric|min:1',
            'totalAmount' => 'required|numeric|min:0',
        ]);

        $sale = Sale::create([
            'pdt_id' => $request->pdt_id,
            'quantity' => $request->quantity,
            'totalAmount' => $request->totalAmount,
        ]);

        $topProducts = Sale::select('pdt_id')
            ->selectRaw('SUM(quantity) as total_sold, SUM(totalAmount) as total_amount')
            ->with('product')
            ->groupBy('pdt_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $sales = Sale::with('product')
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Sale recorded successfully!',
            'topProducts' => $topProducts,
            'sales' => $sales,
        ]);
    }

    // Reports view (HTML)
    public function reports()
    {
        $sales = Sale::with('product')->latest()->get();

        $topProducts = Sale::select('pdt_id')
            ->selectRaw('SUM(quantity) as total_sold, SUM(totalAmount) as total_amount')
            ->with('product')
            ->groupBy('pdt_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('totalAmount');
        $totalProducts = $sales->sum('quantity');

        $generatedAt = now()->format('d M Y H:i'); // pass exact time

        return view('sales.reports', compact(
            'sales',
            'topProducts',
            'totalSales',
            'totalRevenue',
            'totalProducts',
            'generatedAt'
        ));
    }


    // Download report as PDF
    public function downloadReport()
    {
        $sales = Sale::with('product')->latest()->get();

        $topProducts = Sale::select('pdt_id')
            ->selectRaw('SUM(quantity) as total_sold, SUM(totalAmount) as total_amount')
            ->with('product')
            ->groupBy('pdt_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('totalAmount');
        $totalProducts = $sales->sum('quantity');

        $generatedAt = now()->format('d M Y H:i'); // exact time for PDF

        $pdf = Pdf::loadView('sales.reports', compact(
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
