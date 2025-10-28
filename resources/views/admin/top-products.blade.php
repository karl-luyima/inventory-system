@extends('layouts.admin')

@section('page-title', 'Top Performing Products')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-5xl mx-auto space-y-6">
    <!-- Chart -->
    <div class="bg-gray-50 p-4 rounded shadow mb-6">
        <canvas id="topProductsChart" class="w-full h-64"></canvas>
    </div>

    <!-- Table -->
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-300 px-4 py-2">Product Name</th>
                <th class="border border-gray-300 px-4 py-2">Total Quantity Sold</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $product)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $product->pdt_name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $product->total_qty }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    const topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topProducts->pluck('pdt_name')) !!},
            datasets: [{
                label: 'Quantity Sold',
                data: {!! json_encode($topProducts->pluck('total_qty')) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: 'rgba(5, 150, 105, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity Sold'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Products'
                    }
                }
            }
        }
    });
</script>
@endsection
