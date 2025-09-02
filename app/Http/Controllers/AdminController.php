<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;

class AdminController extends Controller
{
    // Admin Dashboard
    public function dashboard()
    {
        return view('admin.dashboard', [
            'usersCount'   => User::count(),
            'productsCount'=> Product::sum('stock'),
            'monthlySales' => Sale::whereMonth('created_at', now()->month)->sum('amount'),

            // Chart data
            'salesMonths'  => Sale::selectRaw('MONTHNAME(created_at) as month')
                                ->groupBy('month')->pluck('month'),
            'salesData'    => Sale::selectRaw('SUM(amount) as total')
                                ->groupByRaw('MONTH(created_at)')->pluck('total'),
            'productNames' => Product::pluck('name'),
            'productStock' => Product::pluck('stock'),
        ]);
    }
}
