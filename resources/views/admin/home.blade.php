@extends('layouts.admin')

@section('title', 'Admin Home')
@section('page-title', '⚙️ Admin Home')

@section('content')
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Welcome, {{ session('role') == 'admin' ? 'Admin' : 'User' }} 👋
        </h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md">
                Logout
            </button>
        </form>
    </div>

    <!-- Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Line Chart -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Monthly Sales</h2>
            <canvas id="salesChart" class="w-full h-64"></canvas>
        </div>

        <!-- Products Bar Chart -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Top Products</h2>
            <canvas id="productsChart" class="w-full h-64"></canvas>
        </div>
    </div>
@endsection
