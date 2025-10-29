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
        // Reuse home() to pass necessary variables
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
        $data = [
            'total_users' => InventoryClerk::count() + SalesAnalyst::count(),
            'active_kpis' => Kpi::count(),
            'total_products' => Product::count(),
            'low_stock_items' => Product::where('stock_level', '<=', 5)
                ->get(['pdt_name as name', 'stock_level'])
                ->toArray(),
            'top_products' => DB::table('products')
                ->select(
                    'pdt_name as name',
                    'price as unit_price',
                    DB::raw('(SELECT SUM(quantity) FROM sales WHERE sales.pdt_id = products.pdt_id) as quantity_sold'),
                    DB::raw('(price * (SELECT SUM(quantity) FROM sales WHERE sales.pdt_id = products.pdt_id)) as total_ksh')
                )
                ->orderByDesc('quantity_sold')
                ->limit(5)
                ->get()
                ->toArray()
        ];

        Report::create([
            'name' => 'Admin Dashboard Summary',
            'creator_type' => null,
            'creator_id' => null,
            'creator_name' => null,
            'data' => json_encode($data),
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

    // ================= Top Products =================
    public function topProducts()
    {
        $topProducts = DB::table('products')
            ->select(
                'pdt_name as name',
                'price as unit_price',
                DB::raw('(SELECT SUM(quantity) FROM sales WHERE sales.pdt_id = products.pdt_id) as quantity_sold'),
                DB::raw('(price * (SELECT SUM(quantity) FROM sales WHERE sales.pdt_id = products.pdt_id)) as total_ksh')
            )
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
}
