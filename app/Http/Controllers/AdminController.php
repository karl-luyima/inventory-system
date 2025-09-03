<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;

class AdminController extends Controller
{
    public function dashboard()
    {
        $usersCount = User::count();
        $productsCount = Product::count();
        $monthlySales = Sale::sum('amount'); // adjust logic if needed

        return view('admin.dashboard', compact('usersCount', 'productsCount', 'monthlySales'));
    }

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function inventory()
    {
        $products = Product::all();
        return view('admin.inventory', compact('products'));
    }

    public function sales()
    {
        $sales = Sale::all();
        return view('admin.sales', compact('sales'));
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function kpis()
    {
        return view('admin.kpis');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
