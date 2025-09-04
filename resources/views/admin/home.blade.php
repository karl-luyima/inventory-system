@extends('layouts.admin')

@section('title', 'Admin Home')
@section('page-title', '⚙️ Admin Home')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg rounded-r-2xl p-6">
        <h2 class="text-2xl font-bold text-blue-600 mb-8">Admin Panel</h2>

        <nav class="space-y-4">
            <a href="{{ route('admin.users') }}" class="flex items-center p-3 rounded-lg hover:bg-blue-100 transition">
                <span class="material-icons-outlined mr-3 text-blue-600">groups</span>
                <span class="font-medium text-gray-700">Users</span>
            </a>
            <a href="{{ route('admin.inventory') }}" class="flex items-center p-3 rounded-lg hover:bg-blue-100 transition">
                <span class="material-icons-outlined mr-3 text-green-600">inventory_2</span>
                <span class="font-medium text-gray-700">Inventory</span>
            </a>
            <a href="{{ route('admin.sales') }}" class="flex items-center p-3 rounded-lg hover:bg-blue-100 transition">
                <span class="material-icons-outlined mr-3 text-purple-600">attach_money</span>
                <span class="font-medium text-gray-700">Sales</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="flex items-center p-3 rounded-lg hover:bg-blue-100 transition">
                <span class="material-icons-outlined mr-3 text-orange-600">bar_chart</span>
                <span class="font-medium text-gray-700">Reports</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center p-3 rounded-lg hover:bg-blue-100 transition">
                <span class="material-icons-outlined mr-3 text-gray-600">settings</span>
                <span class="font-medium text-gray-700">Settings</span>
            </a>
            <a href="{{ route('admin.kpis') }}" class="flex items-center p-3 rounded-lg hover:bg-blue-100 transition">
                <span class="material-icons-outlined mr-3 text-pink-600">dashboard_customize</span>
                <span class="font-medium text-gray-700">Set KPIs</span>
            </a>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Welcome, {{ session('role') == 'admin' ? 'Admin' : 'User' }} 👋
            </h1>
            <div class="flex items-center space-x-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow text-center">
                <h2 class="text-gray-700 font-medium">Total Users</h2>
                <p class="text-3xl font-bold text-blue-600 mt-2">120</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow text-center">
                <h2 class="text-gray-700 font-medium">Active KPIs</h2>
                <p class="text-3xl font-bold text-green-600 mt-2">8</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow text-center">
                <h2 class="text-gray-700 font-medium">Pending Reports</h2>
                <p class="text-3xl font-bold text-red-600 mt-2">5</p>
            </div>
        </div>
    </main>
</div>
@endsection
