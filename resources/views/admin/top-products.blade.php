@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-md">
    <h2 class="text-lg font-semibold mb-4">Top Products</h2>

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
            <tr>
                <td class="px-4 py-2 border-b">{{ $product->name }}</td>
                <td class="px-4 py-2 border-b">{{ $product->quantity_sold ?? 0 }}</td>
                <td class="px-4 py-2 border-b">{{ number_format($product->unit_price, 2) }}</td>
                <td class="px-4 py-2 border-b">{{ number_format($product->total_ksh ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
