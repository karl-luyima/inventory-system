<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Inventory;

class DashboardController extends Controller
{
    public function index()
    {
        // Sales Overview
        $todaySales = Sale::whereDate('created_at', today())->sum('amount');
        $weekSales = Sale::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');
        $monthSales = Sale::whereMonth('created_at', now()->month)->sum('amount');

        // Inventory
        $totalProducts = Inventory::sum('stock');
        $lowStockItems = Inventory::where('stock', '<', 10)->get();

        return view('generaldashboard.index', compact(
            'todaySales', 'weekSales', 'monthSales',
            'totalProducts', 'lowStockItems'
        ));
    }

    // Live data endpoint (for AJAX updates)
    public function getData()
    {
        $todaySales = Sale::whereDate('created_at', today())->sum('amount');
        $weekSales = Sale::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');
        $monthSales = Sale::whereMonth('created_at', now()->month)->sum('amount');

        $totalProducts = Inventory::sum('stock');
        $lowStockItems = Inventory::where('stock', '<', 10)->get();

        return response()->json([
            'todaySales' => $todaySales,
            'weekSales' => $weekSales,
            'monthSales' => $monthSales,
            'totalProducts' => $totalProducts,
            'lowStockItems' => $lowStockItems,
        ]);
    }
}
