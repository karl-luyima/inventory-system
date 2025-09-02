
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;

class SalesAnalystController extends Controller
{
    // Sales Analyst Dashboard
    public function dashboard()
    {
        return view('sales.dashboard', [
            'monthlySales' => Sale::whereMonth('created_at', now()->month)->sum('amount'),
            'yearlySales'  => Sale::whereYear('created_at', now()->year)->sum('amount'),
            'topProducts'  => Product::withSum('sales', 'amount')
                                    ->orderByDesc('sales_sum_amount')
                                    ->take(5)
                                    ->get(),

            // Chart data
            'salesMonths'  => Sale::selectRaw('MONTHNAME(created_at) as month')
                                ->groupBy('month')->pluck('month'),
            'salesData'    => Sale::selectRaw('SUM(amount) as total')
                                ->groupByRaw('MONTH(created_at)')->pluck('total'),
        ]);
    }
}
