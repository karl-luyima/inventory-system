<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesAnalystController extends Controller
{
    // ================= Dashboard =================
    public function dashboard()
    {
        $sales = Sale::with('product')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $topProducts = Sale::select('pdt_id')
            ->selectRaw('SUM(quantity) as total_sold, SUM(totalAmount) as total_amount')
            ->with('product')
            ->groupBy('pdt_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('sales.dashboard', compact('sales', 'topProducts'));
    }

    // ================= Record a Sale =================
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

        $sales = Sale::with('product')->latest()->take(10)->get();

        return response()->json([
            'success' => true,
            'message' => 'Sale recorded successfully!',
            'topProducts' => $topProducts,
            'sales' => $sales,
        ]);
    }

    // ================= Generate Report =================
    public function generateReport(Request $request)
    {
        $topProducts = $request->input('topProducts', []); // array of top products

        $report = Report::create([
            'name' => 'Top Products Report ' . now()->format('d M Y H:i'),
            'creator_type' => 'analyst',
            'creator_id' => 0, // no auth, default to 0
            'data' => json_encode($topProducts),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report generated successfully!',
            'report_id' => $report->id,
        ]);
    }

    // ================= Reports View =================
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

        $generatedAt = now()->format('d M Y H:i');

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
