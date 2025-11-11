<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;
use App\Models\Kpi;
use App\Models\Product;
use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ================= Dashboard =================
    public function dashboard()
    {
        return $this->home();
    }

    public function home()
    {
        $totalUsers = InventoryClerk::count() + SalesAnalyst::count();
        $activeKpis = Kpi::count();

        return view('admin.home', compact('totalUsers', 'activeKpis'));
    }

    // ================= Inventory =================
    public function inventory()
    {
        $products = Product::paginate(10);
        return view('admin.inventory', compact('products'));
    }

    // ================= Users =================
    public function users()
    {
        $inventoryClerks = InventoryClerk::all()->map(fn($user) => (object)[
            'id' => $user->clerk_id,
            'name' => $user->clerk_name,
            'email' => $user->clerk_email,
            'role' => 'Inventory Clerk',
            'type' => 'inventory'
        ]);

        $salesAnalysts = SalesAnalyst::all()->map(fn($user) => (object)[
            'id' => $user->analyst_id,
            'name' => $user->analyst_name,
            'email' => $user->analyst_email,
            'role' => 'Sales Analyst',
            'type' => 'sales'
        ]);

        $users = $inventoryClerks->concat($salesAnalysts);

        $page = request()->get('page', 1);
        $perPage = 10;

        $paginatedUsers = new LengthAwarePaginator(
            $users->forPage($page, $perPage),
            $users->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.users', ['users' => $paginatedUsers]);
    }

    // ================= Delete User =================
    public function deleteUser(Request $request, $id)
    {
        $type = $request->query('type');

        $user = match ($type) {
            'inventory' => InventoryClerk::find($id),
            'sales' => SalesAnalyst::find($id),
            default => null
        };

        if (!$user) return redirect()->route('admin.users')->with('error', 'User not found.');

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }

    // ================= Reports =================
    public function reports()
    {
        $reports = Report::orderByDesc('created_at')->paginate(10);
        return view('admin.reports', compact('reports'));
    }

    public function generateSummaryReport()
    {
        // Total metrics
        $totalUsers = InventoryClerk::count() + SalesAnalyst::count();
        $activeKpis = Kpi::count();
        $totalProducts = Product::count();

        // Low stock items
        $lowStockItems = Product::where('stock_level', '<=', 5)
            ->get(['pdt_name', 'stock_level'])
            ->map(fn($item) => [
                'name' => $item->pdt_name,
                'stock_level' => $item->stock_level,
            ]);

        // Top products (sum sales quantity)
        $topProducts = DB::table('products')
            ->leftJoin('sales', 'products.pdt_id', '=', 'sales.pdt_id')
            ->select(
                'products.pdt_name',
                'products.price',
                DB::raw('COALESCE(SUM(sales.quantity), 0) as sales_sum_quantity'),
                DB::raw('COALESCE(products.price * SUM(sales.quantity), 0) as total_sales_ksh')
            )
            ->groupBy('products.pdt_id', 'products.pdt_name', 'products.price')
            ->orderByDesc('sales_sum_quantity')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'pdt_name' => $p->pdt_name,
                'sales_sum_quantity' => $p->sales_sum_quantity,
                'unit_price' => $p->price,
                'total_sales_ksh' => $p->total_sales_ksh,
            ]);

        // Save report
        Report::create([
            'name' => 'Admin Dashboard Summary',
            'creator_type' => null,
            'creator_id' => null,
            'creator_name' => null,
            'data' => json_encode([
                'total_users' => $totalUsers,
                'active_kpis' => $activeKpis,
                'total_products' => $totalProducts,
                'low_stock_items' => $lowStockItems,
                'top_products' => $topProducts,
            ]),
        ]);

        return redirect()->route('admin.reports')
            ->with('success', 'Admin summary report generated successfully!');
    }

    public function viewReport($id)
    {
        $report = Report::findOrFail($id);
        $data = json_decode($report->data, true);

        return view('admin.view-report', compact('report', 'data'));
    }

    public function downloadSummaryReport($id)
    {
        $report = Report::findOrFail($id);
        $data = json_decode($report->data, true);

        $pdf = Pdf::loadView('admin.report-pdf', [
            'report' => $report,
            'data' => $data
        ]);

        return $pdf->download("Admin_Report_{$report->id}.pdf");
    }

    // ================= KPIs =================
    public function kpis()
    {
        $kpis = Kpi::paginate(10);
        return view('admin.kpis', compact('kpis'));
    }

    public function addKpi(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'color' => 'required|string|in:blue,green,yellow,red,purple',
        ]);

        Kpi::create([
            'title' => $request->title,
            'value' => $request->value,
            'color' => $request->color,
        ]);

        return redirect()->route('admin.kpis')->with('success', 'KPI added successfully!');
    }

    public function editKpi($id)
    {
        $kpi = Kpi::findOrFail($id);
        return view('admin.edit-kpi', compact('kpi'));
    }

    public function updateKpi(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'color' => 'required|string|in:blue,green,yellow,red,purple',
        ]);

        $kpi = Kpi::findOrFail($id);
        $kpi->update([
            'title' => $request->title,
            'value' => $request->value,
            'color' => $request->color,
        ]);

        return redirect()->route('admin.kpis')->with('success', 'KPI updated successfully!');
    }

    public function deleteKpi($id)
    {
        $kpi = Kpi::findOrFail($id);
        $kpi->delete();

        return redirect()->route('admin.kpis')->with('success', 'KPI deleted successfully!');
    }

    // ================= Top Products =================
    public function topProducts()
    {
        $topProducts = DB::table('products')
            ->leftJoin('sales', 'products.pdt_id', '=', 'sales.pdt_id')
            ->select(
                'products.pdt_name as name',
                'products.price as unit_price',
                DB::raw('COALESCE(SUM(sales.quantity), 0) as quantity_sold'),
                DB::raw('COALESCE(products.price * SUM(sales.quantity), 0) as total_ksh')
            )
            ->groupBy('products.pdt_id', 'products.pdt_name', 'products.price')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();

        return view('admin.top-products', compact('topProducts'));
    }

    // ================= Settings =================
    public function settings()
    {
        return view('admin.settings');
    }

    // ================= Delete Report =================
    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return redirect()->route('admin.reports')->with('success', 'Report deleted successfully!');
    }

    // ================= Inventory Data =================
    public function inventoryData()
    {
        $totalProducts = Product::count();
        $lowStockItems = Product::where('stock_level', '<=', 5)
            ->get(['pdt_name as name', 'stock_level as stock']);

        return response()->json([
            'totalProducts' => $totalProducts,
            'lowStockItems' => $lowStockItems,
        ]);
    }
}
