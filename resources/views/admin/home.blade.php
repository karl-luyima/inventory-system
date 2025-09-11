@extends('layouts.admin')

@section('title', 'Admin Home')
@section('page-title', '⚙️ Admin Home')

@section('content')
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Welcome, {{ session('name', 'Admin') }} ({{ ucfirst(session('role', 'Admin')) }}) 👋
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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-gray-700 font-medium">Total Users</h2>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-gray-700 font-medium">Active KPIs</h2>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeKpis }}</p>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- Monthly Sales Line Chart ---
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesMonths) !!},
            datasets: [{
                label: 'Monthly Sales',
                data: {!! json_encode($salesData) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // --- Top Products Bar Chart ---
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    new Chart(productsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($productNames) !!},
            datasets: [{
                label: 'Quantity Sold',
                data: {!! json_encode($productStock) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: 'rgba(5, 150, 105, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>  
@endsection
