@extends('layouts.admin')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Report Details</h1>
        <a href="{{ route('admin.reports.download', $report->id) }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-medium
                  bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700
                  shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition duration-200">
            <i data-lucide="download" class="w-5 h-5"></i>
            Download PDF
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ $report->name }}</h2>
        <p class="text-gray-500 text-sm mb-6">
            Created on: {{ $report->created_at->format('d M Y, H:i') }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-100 rounded-xl p-4 text-center">
                <h3 class="text-gray-600">Total Users</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $data['total_users'] ?? 0 }}</p>
            </div>
            <div class="bg-gray-100 rounded-xl p-4 text-center">
                <h3 class="text-gray-600">Total Products</h3>
                <p class="text-2xl font-bold text-green-600">{{ $data['total_products'] ?? 0 }}</p>
            </div>
            <div class="bg-gray-100 rounded-xl p-4 text-center">
                <h3 class="text-gray-600">Active KPIs</h3>
                <p class="text-2xl font-bold text-purple-600">{{ $data['active_kpis'] ?? 0 }}</p>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Top Products</h3>
            <ul class="list-disc pl-6 space-y-1 text-gray-600">
                @forelse ($data['top_products'] ?? [] as $product)
                    <li>{{ $product['pdt_name'] ?? 'N/A' }} — {{ $product['sales_sum_quantity'] ?? 0 }} sales</li>
                @empty
                    <li>No top products available.</li>
                @endforelse
            </ul>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Low Stock Items</h3>
            <ul class="list-disc pl-6 space-y-1 text-gray-600">
                @forelse ($data['low_stock_items'] ?? [] as $item)
                    <li>{{ $item['name'] ?? 'N/A' }} — {{ $item['stock_level'] ?? 0 }} in stock</li>
                @empty
                    <li>No low stock items found.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
