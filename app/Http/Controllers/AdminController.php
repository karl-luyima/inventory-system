<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;
use App\Models\Kpi;
use App\Models\Product;
use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    // ================= Dashboard =================
    public function home()
    {
        $totalUsers = InventoryClerk::count() + SalesAnalyst::count();
        $activeKpis = Kpi::count();

        return view('admin.home', compact('totalUsers', 'activeKpis'));
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

    public function deleteKpi($id)
    {
        $kpi = Kpi::findOrFail($id);
        $kpi->delete();

        return redirect()->route('admin.kpis')->with('success', 'KPI removed successfully!');
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

    // ================= Top Products =================
    public function topProducts()
    {
        $topProducts = Product::withSum('sales', 'quantity')
            ->orderByDesc('sales_sum_quantity')
            ->take(10)
            ->get()
            ->filter(fn($product) => $product->sales_sum_quantity > 0)
            ->map(fn($product) => (object)[
                'pdt_name' => $product->pdt_name,
                'total_qty' => $product->sales_sum_quantity
            ]);

        return view('admin.top-products', compact('topProducts'));
    }

    // ================= Users =================
    public function users()
    {
        $clerks = InventoryClerk::all()->map(fn($u) => (object)[
            'role' => 'Inventory Clerk',
            'id' => $u->clerk_id,
            'name' => $u->clerk_name,
            'email' => $u->clerk_email,
            'type' => 'clerk'
        ]);

        $analysts = SalesAnalyst::all()->map(fn($u) => (object)[
            'role' => 'Sales Analyst',
            'id' => $u->analyst_id,
            'name' => $u->analyst_name,
            'email' => $u->analyst_email,
            'type' => 'analyst'
        ]);

        $allUsers = $clerks->merge($analysts)->values();

        $page = request()->get('page', 1);
        $perPage = 10;
        $paginated = new LengthAwarePaginator(
            $allUsers->forPage($page, $perPage),
            $allUsers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.users', ['users' => $paginated]);
    }

    public function deleteUser(Request $request, $id)
    {
        $type = $request->get('type');

        if ($type === 'clerk') {
            $user = InventoryClerk::findOrFail($id);
        } elseif ($type === 'analyst') {
            $user = SalesAnalyst::findOrFail($id);
        } else {
            return redirect()->back()->with('error', 'Invalid user type.');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }

    // ================= Reports =================
    public function reports()
    {
        $reports = Report::orderByDesc('created_at')->paginate(10);
        return view('admin.reports', compact('reports'));
    }

    public function viewReport($id)
    {
        $report = Report::findOrFail($id);
        $topProducts = json_decode($report->data, true);

        return view('admin.view-report', compact('report', 'topProducts'));
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->route('admin.reports')->with('success', 'Report deleted successfully!');
    }

    public function generateSummaryReport()
    {
        $data = [
            'total_users' => InventoryClerk::count() + SalesAnalyst::count(),
            'active_kpis' => Kpi::count(),
            'total_products' => Product::count(),
            'low_stock_items' => Product::where('stock_level', '<=', 5)
                ->get(['pdt_name as name', 'stock_level'])
                ->toArray(),
            'top_products' => Product::withSum('sales', 'quantity')
                ->orderByDesc('sales_sum_quantity')
                ->take(5)
                ->get(['pdt_name', 'sales_sum_quantity'])
                ->toArray()
        ];

        $report = Report::create([
            'name' => 'Admin Dashboard Summary',
            'creator_type' => 'admin',
            'creator_id' => 1,
            'data' => json_encode($data),
        ]);

        return redirect()->route('admin.reports')->with('success', 'Admin summary report generated successfully!');
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
