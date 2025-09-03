<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Administrator;
use App\Models\InventoryClerk;
use App\Models\SalesAnalyst;
use App\Models\Kpi;

class AdminController extends Controller
{
    // ================= Users =================
    public function users(Request $request)
    {
        // Handle filter
        $status = $request->get('status', 'all');
        $query = User::query();

        if ($status === 'active') {
            $query->where('active', 1);
        } elseif ($status === 'inactive') {
            $query->where('active', 0);
        }

        // Paginate users
        $users = $query->paginate(10);

        // Attach role dynamically
        foreach ($users as $user) {
            if (Administrator::where('user_id', $user->user_id)->exists()) {
                $user->role = 'Administrator';
            } elseif (InventoryClerk::where('user_id', $user->user_id)->exists()) {
                $user->role = 'Inventory Clerk';
            } elseif (SalesAnalyst::where('user_id', $user->user_id)->exists()) {
                $user->role = 'Sales Analyst';
            } else {
                $user->role = 'Unknown';
            }
        }

        // Overview counts
        $totalUsers    = User::count();
        $activeUsers   = User::where('active', 1)->count();
        $inactiveUsers = User::where('active', 0)->count();

        return view('admin.users', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'status'
        ));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Delete from role-specific tables too
        Administrator::where('user_id', $user->user_id)->delete();
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

    // Edit form
    public function editKpi($id)
    {
        $kpi = Kpi::findOrFail($id);
        return view('admin.edit-kpi', compact('kpi'));
    }

    // Update KPI
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
}
