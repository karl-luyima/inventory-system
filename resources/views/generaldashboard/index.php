@extends('layouts.admin')

@section('title', 'General Dashboard')
@section('page-title', '📊 General Dashboard')

@section('content')
<div class="p-8 space-y-6">

    {{-- Sales Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">Today’s Sales</h2>
            <p id="todaySales" class="text-3xl font-bold text-blue-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">This Week</h2>
            <p id="weekSales" class="text-3xl font-bold text-green-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">This Month</h2>
            <p id="monthSales" class="text-3xl font-bold text-purple-600 mt-2">0</p>
        </div>
    </div>

    {{-- Inventory Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-semibold text-gray-700">Total Stock</h2>
            <p id="totalProducts" class="text-3xl font-bold text-orange-600 mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-lg font-semibold text-gray-700">Low Stock Alerts</h2>
            <ul id="lowStockList" class="mt-3 text-red-600 list-disc pl-6 space-y-1">
                <li>Loading...</li>
            </ul>
        </div>
    </div>

</div>

{{-- AJAX for real-time updates --}}
<script>
    function fetchDashboardData() {
        fetch("{{ route('general.dashboard.data') }}")
            .then(response => response.json())
            .then(data => {
                // Update sales
                document.getElementById('todaySales').innerText = data.todaySales;
                document.getElementById('weekSales').innerText = data.weekSales;
                document.getElementById('monthSales').innerText = data.monthSales;

                // Update inventory
                document.getElementById('totalProducts').innerText = data.totalProducts;

                // Low stock list
                let list = document.getElementById('lowStockList');
                list.innerHTML = "";
                if (data.lowStockItems.length > 0) {
                    data.lowStockItems.forEach(item => {
                        let li = document.createElement('li');
                        li.innerText = `${item.name} (Stock: ${item.stock})`;
                        list.appendChild(li);
                    });
                } else {
                    list.innerHTML = "<li>No low stock items 🎉</li>";
                }
            })
            .catch(error => console.error("Error fetching dashboard data:", error));
    }

    // Run once when page loads
    fetchDashboardData();

    // Refresh every 10 seconds
    setInterval(fetchDashboardData, 10000);
</script>
@endsection
