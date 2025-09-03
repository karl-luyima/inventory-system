@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', '📊 Reports')

@section('content')
<div class="p-8 space-y-6">

    {{-- Download Button --}}
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.reports.download') }}" 
           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            ⬇️ Download Report (PDF)
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-lg font-semibold">Monthly Sales Report</h2>
            <canvas id="monthlySalesChart"></canvas>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-lg font-semibold">Inventory Levels</h2>
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>
</div>

<script>
    new Chart(document.getElementById('monthlySalesChart'), {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar"],
            datasets: [{ label: 'Sales ($)', data: [5000, 7000, 8000], borderColor: '#2563eb' }]
        }
    });

    new Chart(document.getElementById('inventoryChart'), {
        type: 'bar',
        data: {
            labels: ["Laptops", "Keyboards", "Mice"],
            datasets: [{ label: 'Stock', data: [120, 80, 60], backgroundColor: '#16a34a' }]
        }
    });
</script>
@endsection
