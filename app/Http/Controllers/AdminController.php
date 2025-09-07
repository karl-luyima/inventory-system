<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Administrator;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;
use App\Models\Kpi;
use App\Models\Sale;
use App\Models\Product;

class AdminController extends Controller
{
    // ================= Dashboard =================
    public function home()
    {
        // Total users
        $totalUsers = User::count();

        // Total KPIs
        $activeKpis = Kpi::count();

        // --- Monthly Sales (this year) ---
        $monthlySales = Sale::selectRaw('MONTH(`date`) as month, SUM(totalAmount) as total')
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $salesData = [];
        $salesMonths = [];
        for ($i = 1; $i <= 12; $i++) {
            $salesMonths[] = date("M", mktime(0, 0, 0, $i, 1));
            $salesData[] = $monthlySales[$i] ?? 0;
        }

        // --- Top 5 products by quantity sold ---
        $topProducts = Sale::join('products', 'sales.pdt_id', '=', 'products.pdt_id')
            ->selectRaw('products.pdt_name, SUM(sales.quantity) as total_qty')
            ->groupBy('products.pdt_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $productNames = $topProducts->pluck('pdt_name');
        $productStock = $topProducts->pluck('total_qty');

        return view('admin.home', compact(
            'totalUsers',
            'activeKpis',
            'salesData',
            'salesMonths',
            'productNames',
            'productStock'
        ));
    }

    // ================= KPIs =================
    public function kpis()
    {
        $kpis = Kpi::all();
        return view('admin.kpis', compact('kpis'));
    }

    public function addKpi(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'value' => 'required',
            'color' => 'required|in:blue,green,yellow,red,purple'
        ]);

        Kpi::create($request->only('title', 'value', 'color'));

        return redirect()->route('admin.kpis')->with('success', 'KPI added successfully!');
    }

    public function deleteKpi($id)
    {
        $kpi = Kpi::findOrFail($id);
        $kpi->delete();

        return redirect()->route('admin.kpis')->with('success', 'KPI removed successfully!');
    }

    public function editKpi($id)
    {
        $kpi = Kpi::findOrFail($id);
        return view('admin.edit-kpi', compact('kpi'));
    }

    public function updateKpi(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'value' => 'required',
            'color' => 'required|in:blue,green,yellow,red,purple'
        ]);

        $kpi = Kpi::findOrFail($id);
        $kpi->update($request->only('title', 'value', 'color'));

        return redirect()->route('admin.kpis')->with('success', 'KPI updated successfully!');
    }

    // ================= Inventory =================
    public function inventory()
    {
        return view('admin.inventory');
    }

    public function inventoryData()
    {
        $totalProducts = Product::count();

        $lowStockItems = Product::where('stock_level', '<=', 5)
            ->get(['pdt_name as name', 'stock_level']);

        $chartData = Product::select('pdt_name as name', 'stock_level')->get();

        return response()->json([
            'totalProducts' => $totalProducts,
            'lowStockItems' => $lowStockItems,
            'chart' => $chartData
        ]);
    }

    // ================= Sales =================
    public function sales()
    {
        return view('admin.sales');
    }

    public function salesData()
    {
        $sales = Sale::selectRaw('DATE(date) as sale_date, SUM(totalAmount) as total_sales')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'asc')
            ->get();

        return response()->json($sales);
    }

    // ================= Reports =================
    public function reports()
    {
        return view('admin.reports');
    }

    public function reportsData()
    {
        $reportData = Sale::selectRaw('MONTH(date) as month, SUM(totalAmount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($reportData);
    }

    public function downloadReport()
    {
        return response()->download(storage_path('reports/sample-report.pdf'));
    }

    // ================= Settings =================
    public function settings()
    {
        return view('admin.settings');
    }

    public function dashboard()
    {
        return redirect()->route('admin.home');
    }
}
