@extends('layouts.salesanalyst')

@section('page-title', 'Forecast Chart')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-semibold mb-4">
        Forecast Chart — {{ $product->name }}
    </h2>

    <p class="text-gray-600 mb-6">
        Visualizing future predicted sales and historical trends.
    </p>

    {{-- Chart Canvas --}}
    <canvas id="forecastChart" height="120"></canvas>

    {{-- Overall chart explanation --}}
    <div class="mt-4 p-4 bg-gray-100 rounded">
        <strong>Automatic Chart Explanation:</strong>
        <p>{{ $chartExplanation ?? 'No explanation available.' }}</p>
    </div>

    {{-- Detailed per-forecast explanations --}}
    <h3 class="mt-6 text-xl font-semibold">Per-Forecast Explanations</h3>
    <table class="min-w-full mt-2 border">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left">Forecast Date</th>
                <th class="px-4 py-2 text-left">Predicted Sales (Units)</th>
                <th class="px-4 py-2 text-left">Explanation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($forecastData as $forecast)
            <tr class="border-t">
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($forecast->forecast_date)->format('M d, Y') }}</td>
                <td class="px-4 py-2 font-medium">{{ number_format(round($forecast->predicted_sales), 0) }} Units</td>
                <td class="px-4 py-2">{{ $forecast->explanation_text }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    // Forecast labels and values
    const forecastLabels = @json($forecastData->pluck('forecast_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d')));
    const forecastValues = @json($forecastData->pluck('predicted_sales')->map(fn($v) => round($v)));

    // Historical labels and values
    const historicalLabels = @json($historicalSales->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d')));
    const historicalValues = @json($historicalSales->pluck('quantity')->map(fn($v) => round($v)));

    const ctx = document.getElementById('forecastChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [...historicalLabels, ...forecastLabels],
            datasets: [
                {
                    label: 'Historical Sales',
                    data: historicalValues,
                    borderColor: 'blue',
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3
                },
                {
                    label: 'Forecasted Sales',
                    data: Array(historicalValues.length).fill(null).concat(forecastValues),
                    borderColor: 'green',
                    borderDash: [5, 5],
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

});
</script>
@endsection
