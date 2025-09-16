<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;
use App\Models\Kpi;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminController extends Controller
{
    // ================= Dashboard =================
    public function home()
    {
        $activeKpis = Kpi::count();

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

        $topProducts = Sale::join('products', 'sales.pdt_id', '=', 'products.pdt_id')
            ->selectRaw('products.pdt_name, SUM(sales.quantity) as total_qty')
            ->groupBy('products.pdt_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $productNames = $topProducts->pluck('pdt_name');
        $productStock = $topProducts->pluck('total_qty');

        return view('admin.home', compact(
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
        $topProducts = Sale::join('products', 'sales.pdt_id', '=', 'products.pdt_id')
            ->selectRaw('products.pdt_name, SUM(sales.totalAmount) as total_sales')
            ->groupBy('products.pdt_name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        return response()->json([
            'labels' => $topProducts->pluck('pdt_name'),
            'values' => $topProducts->pluck('total_sales'),
        ]);
    }

    // ================= Users =================
    public function users()
    {
        $clerks = InventoryClerk::all()->map(function ($u) {
            $u->role = 'Inventory Clerk';
            $u->id = $u->clerk_id;
            $u->name = $u->clerk_name;
            $u->email = $u->clerk_email;
            $u->type = 'clerk';
            return $u;
        });

        $analysts = SalesAnalyst::all()->map(function ($u) {
            $u->role = 'Sales Analyst';
            $u->id = $u->analyst_id;
            $u->name = $u->analyst_name;
            $u->email = $u->analyst_email;
            $u->type = 'analyst';
            return $u;
        });

        $allUsers = $clerks->merge($analysts)->values();

        // Pagination
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
        // Only sales analyst generated reports
        $reports = Report::where('creator_type', 'analyst')
                         ->orderByDesc('created_at')
                         ->paginate(10);

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
