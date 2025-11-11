@extends('layouts.salesanalyst')

@section('page-title', 'Sales Reports')

@section('content')
<div class="max-w-7xl mx-auto p-6 bg-white rounded shadow">

    {{-- Header with Download Button --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Sales Reports</h2>
        <a href="{{ route('sales.downloadReport') }}" 
           class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" 
           target="_blank">
            Download PDF
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="p-4 bg-gray-100 rounded shadow">
            <div class="text-gray-500">Total Sales</div>
            <div class="text-xl font-bold">{{ $totalSales }}</div>
        </div>
        <div class="p-4 bg-gray-100 rounded shadow">
            <div class="text-gray-500">Total Revenue</div>
            <div class="text-xl font-bold">Ksh {{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="p-4 bg-gray-100 rounded shadow">
            <div class="text-gray-500">Products Sold</div>
            <div class="text-xl font-bold">{{ $totalProducts }}</div>
        </div>
    </div>

    {{-- Top Products Chart --}}
    <h3 class="text-lg font-semibold mb-2">🔥 Top 5 Best-Selling Products</h3>
    <canvas id="topProductsChart" height="200"></canvas>

    {{-- Detailed Sales Table --}}
    <h3 class="text-lg font-semibold mb-2 mt-6">Detailed Sales</h3>
    <table class="w-full table-auto border border-gray-300">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-2 py-1">Product</th>
                <th class="border px-2 py-1">Quantity</th>
                <th class="border px-2 py-1">Amount</th>
                <th class="border px-2 py-1">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr>
                <td class="border px-2 py-1">{{ $sale->product->pdt_name ?? 'Unknown' }}</td>
                <td class="border px-2 py-1">{{ $sale->quantity }}</td>
                <td class="border px-2 py-1">Ksh {{ number_format($sale->totalAmount, 2) }}</td>
                <td class="border px-2 py-1">
                    @php
                        $saleDate = $sale->created_at ?? $sale->date ?? null;
                    @endphp

                    {{ $saleDate
                        ? \Carbon\Carbon::parse($saleDate)->timezone(config('app.timezone'))->format('d M Y H:i')
                        : 'N/A'
                    }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const topProducts = @json($topProducts);

    const labels = topProducts.map(p => p.pdt_name);
    const data = topProducts.map(p => p.total_sold);

    const ctx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Units Sold',
                data: data,
                backgroundColor: 'rgba(255, 165, 0, 0.7)',
                borderColor: 'rgba(255, 140, 0, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantity Sold' }
                }
            },
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Top 5 Products by Quantity Sold' }
            }
        }
    });
</script>
@endsection
