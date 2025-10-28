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
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ================= Dashboard =================
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
        // Merge users as arrays
        $inventoryClerks = InventoryClerk::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'Inventory Clerk',
                'type' => 'inventory',
            ];
        });

        $salesAnalysts = SalesAnalyst::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'Sales Analyst',
                'type' => 'sales',
            ];
        });

        $users = collect($inventoryClerks)->concat($salesAnalysts);

        // Paginate manually
        $page = request()->get('page', 1);
        $perPage = 10;

        $paginatedUsers = new LengthAwarePaginator(
            $users->forPage($page, $perPage),
            $users->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('admin.users', ['users' => $paginatedUsers]);
    }

    public function deleteUser(Request $request, $id)
    {
        $type = $request->query('type');

        if ($type === 'inventory') {
            $user = InventoryClerk::find($id);
        } elseif ($type === 'sales') {
            $user = SalesAnalyst::find($id);
        } else {
            return redirect()->route('admin.users')->with('error', 'Invalid user type.');
        }

        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User not found.');
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

    public function generateSummaryReport()
    {
        $admin = Auth::user();

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

        Report::create([
            'name' => 'Admin Dashboard Summary',
            'creator_type' => 'admin',
            'creator_id' => $admin ? $admin->id : 1,
            'creator_name' => $admin ? $admin->name : 'System Admin',
            'data' => json_encode($data),
        ]);

        return redirect()->route('admin.reports')->with('success', 'Admin summary report generated successfully!');
    }

    public function viewReport($id)
    {
        $report = Report::findOrFail($id);
        $data = json_decode($report->data, true);

        $data = array_map(function ($value) {
            if (is_array($value)) return $value;
            return is_numeric($value) ? (int)$value : $value;
        }, $data);

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

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->route('admin.reports')->with('success', 'Report deleted successfully!');
    }
}
