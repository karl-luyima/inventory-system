@extends('layouts.admin')

@section('title', 'Report Details')
@section('page-title', '📊 Report Details')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-md">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $report->name }}</h2>
    <p class="text-gray-600 mb-4">Created by: {{ ucfirst($report->creator_type) }} ID: {{ $report->creator_id }}</p>
    <p class="text-gray-600 mb-6">Generated on: {{ $report->created_at->format('d M Y, H:i') }}</p>

    <table class="w-full table-auto border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border-b">#</th>
                <th class="px-4 py-2 border-b text-left">Product Name</th>
                <th class="px-4 py-2 border-b text-left">Quantity Sold</th>
                <th class="px-4 py-2 border-b text-left">Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $index => $product)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border-b">{{ $index + 1 }}</td>
                <td class="px-4 py-2 border-b">{{ $product['pdt_name'] }}</td>
                <td class="px-4 py-2 border-b">{{ $product['total_qty'] }}</td>
                <td class="px-4 py-2 border-b">${{ number_format($product['total_sales'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4 text-gray-500">No products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
