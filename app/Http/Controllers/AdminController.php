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
    // ================= Users & Dashboard =================

    public function home()
    {
        $totalUsers = User::count();
        $activeKpis = Kpi::count();

        // --- Monthly Sales (this year) ---
        $monthlySales = Sale::selectRaw('MONTH(`date`) as month, SUM(totalAmount) as total')
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $salesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $salesData[$i] = $monthlySales[$i] ?? 0;
        }

        // --- Top 5 products by quantity sold ---
        $topProducts = Sale::join('products', 'sales.pdt_id', '=', 'products.pdt_id')
            ->selectRaw('products.pdt_name, SUM(sales.quantity) as total_qty')
            ->groupBy('products.pdt_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->pluck('total_qty', 'pdt_name');

        return view('admin.home', compact(
            'totalUsers',
            'activeKpis',
            'salesData',
            'topProducts'
        ));
    }

    // ================= Users List =================
    public function users()
    {
        // Get all users who are NOT admins
        $users = User::whereNotIn('user_id', Administrator::pluck('user_id'))->paginate(10);

        foreach ($users as $user) {
            if (InventoryClerk::where('user_id', $user->user_id)->exists()) $user->role = 'Inventory Clerk';
            elseif (SalesAnalyst::where('user_id', $user->user_id)->exists()) $user->role = 'Sales Analyst';
            else $user->role = 'Unknown';
        }

        return view('admin.users', compact('users'));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        InventoryClerk::where('user_id', $user->user_id)->delete();
        SalesAnalyst::where('user_id', $user->user_id)->delete();
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
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
        $lowStockItems = Product::where('stock', '<=', 5)
                                ->get(['pdt_name as name', 'stock']);
        $chartData = Product::select('pdt_name as name', 'stock')->get();

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
        // Example: return monthly sales data for AJAX
        $monthlySales = Sale::selectRaw('MONTH(`date`) as month, SUM(totalAmount) as total')
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $salesData = [];
        for ($i = 1; $i <= 12; $i++) $salesData[$i] = $monthlySales[$i] ?? 0;

        return response()->json(['salesData' => $salesData]);
    }

    // ================= Reports =================
    public function reports()
    {
        return view('admin.reports');
    }

    public function reportsData()
    {
        // Example: return report data for AJAX
        $reports = Sale::latest()->take(20)->get();
        return response()->json(['reports' => $reports]);
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
