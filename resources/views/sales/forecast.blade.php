@extends('layouts.salesanalyst')

@section('page-title', 'Sales Forecast')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Sales Forecast</h2>

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
    @if(isset($forecasts) && count($forecasts) > 0)
        <table class="w-full border border-gray-300 rounded mt-4">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Product</th>
                    <th class="border px-4 py-2">Predicted Sales</th>
                    <th class="border px-4 py-2">Explanation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forecasts as $forecast)
                    <tr>
                        <td class="border px-4 py-2">{{ $forecast['product'] }}</td>
                        <td class="border px-4 py-2">{{ $forecast['prediction'] }}</td>
                        <td class="border px-4 py-2">
                            <pre class="whitespace-pre-wrap">{{ $forecast['explanation'] }}</pre>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="mt-4 text-gray-600">No forecast data available.</p>
    @endif
</div>
@endsection
