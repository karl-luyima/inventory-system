@extends('layouts.salesanalyst')

@section('page-title', 'Sales Forecast')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Product Demand Forecasts</h2>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Forecast Table --}}
    @if(isset($groupedForecasts) && count($groupedForecasts) > 0)
        <h3 class="text-lg font-semibold mb-4">Current Weekly Demand Predictions</h3>

        <table class="min-w-full border border-gray-300 rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left w-1/4">Product Name</th>
                    <th class="border px-4 py-2 text-left">Forecast Period</th>
                    <th class="border px-4 py-2 text-right">Min Predicted Weekly Sales</th>
                    <th class="border px-4 py-2 text-right">Max Predicted Weekly Sales</th>
                    <th class="border px-4 py-2 text-center">Details</th>
                </tr>
            </thead>

            <tbody>
                @foreach($groupedForecasts as $productName => $productForecasts)
                    @php
                        $firstForecast = $productForecasts->first();
                        $pdtId = $firstForecast ? $firstForecast->pdt_id : null;
                        $minPrediction = $productForecasts->min('predicted_sales');
                        $maxPrediction = $productForecasts->max('predicted_sales');
                        $startDate = \Carbon\Carbon::parse($productForecasts->min('forecast_date'))->format('M d, Y');
                        $endDate = \Carbon\Carbon::parse($productForecasts->max('forecast_date'))->format('M d, Y');
                    @endphp

                    <tr>
                        <td class="border px-4 py-2 font-medium">{{ $productName }}</td>

                        <td class="border px-4 py-2 text-sm">
                            {{ $startDate }} – {{ $endDate }}
                            ({{ $productForecasts->count() }} Weeks)
                        </td>

                        <td class="border px-4 py-2 text-right text-red-600">
                            KSh {{ number_format($minPrediction, 2) }}
                        </td>

                        <td class="border px-4 py-2 text-right text-green-600">
                            KSh {{ number_format($maxPrediction, 2) }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if($pdtId)
                                <a href="{{ route('sales.forecast.chart', $pdtId) }}" 
                                   class="text-blue-500 hover:text-blue-700 text-sm">
                                   View Chart
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @else
        <p class="mt-4 text-gray-600">
            No product demand forecast data found. Click 'Run New Demand Forecast' above to generate.
        </p>
    @endif
</div>
@endsection
