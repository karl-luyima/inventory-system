@extends('layouts.admin')

@section('title', 'Sales Dashboard')
@section('page-title', '💰 Sales Dashboard')

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

    {{-- Sales Chart Placeholder --}}
    <div class="bg-white p-6 rounded-xl shadow-md mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">📈 Sales Trend</h2>
        <canvas id="salesChart"></canvas>
    </div>

</div>

<script>
    function fetchSalesData() {
        fetch("{{ route('admin.sales.data') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('todaySales').innerText = data.todaySales;
                document.getElementById('weekSales').innerText = data.weekSales;
                document.getElementById('monthSales').innerText = data.monthSales;
                // TODO: update chart with data.chart
            })
            .catch(err => console.error("Error:", err));
    }

    fetchSalesData();
    setInterval(fetchSalesData, 10000);
</script>
@endsection
