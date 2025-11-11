@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-md space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Top Products</h2>
        <span class="text-gray-500 text-sm">Visual representation of top 5 products</span>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border-b text-left">Product</th>
                    <th class="px-4 py-2 border-b text-left">Quantity Sold</th>
                    <th class="px-4 py-2 border-b text-left">Unit Price (Ksh)</th>
                    <th class="px-4 py-2 border-b text-left">Total Sales (Ksh)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b">{{ $product->name }}</td>
                    <td class="px-4 py-2 border-b">{{ $product->quantity_sold ?? 0 }}</td>
                    <td class="px-4 py-2 border-b">{{ number_format($product->unit_price, 2) }}</td>
                    <td class="px-4 py-2 border-b">{{ number_format($product->total_ksh ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Chart Card -->
    <div class="bg-gray-50 rounded-xl p-4 shadow-md">
        <h3 class="text-md font-medium mb-4">Top Products Chart</h3>
        <canvas id="topProductsChart" height="200"></canvas>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    const topProductsData = @json($topProducts);

    const labels = topProductsData.map(p => p.name);
    const quantitySold = topProductsData.map(p => p.quantity_sold || 0);
    const totalSales = topProductsData.map(p => p.total_ksh || 0);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Quantity Sold',
                    data: quantitySold,
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                },
                {
                    label: 'Total Sales (Ksh)',
                    data: totalSales,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString(); // Add thousand separators
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
