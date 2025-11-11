@extends('layouts.inventoryclerk')

@section('page-title', 'Inventory Report Summary')

@section('content')

<div class="bg-white shadow-md rounded p-6 mb-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Inventory Clerk Report Summary</h2>

        <a href="{{ route('clerk.report.download') }}"
            class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition flex items-center gap-2">
            <i data-lucide="download"></i> Download Report (PDF)
        </a>
    </div>

    <p class="text-gray-600 mt-2">
        A consolidated overview of KPIs, total products, and inventories under your management.
    </p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow flex flex-col items-center">
        <i data-lucide="bar-chart-3" class="w-10 h-10 text-green-700"></i>
        <h3 class="mt-2 font-semibold">Total KPIs</h3>
        <p class="text-2xl font-bold">{{ $kpis->count() }}</p>
    </div>

    <div class="bg-white p-6 rounded shadow flex flex-col items-center">
        <i data-lucide="package" class="w-10 h-10 text-green-700"></i>
        <h3 class="mt-2 font-semibold">Total Products</h3>
        <p class="text-2xl font-bold">{{ $products->count() }}</p>
    </div>

    <div class="bg-white p-6 rounded shadow flex flex-col items-center">
        <i data-lucide="archive" class="w-10 h-10 text-green-700"></i>
        <h3 class="mt-2 font-semibold">Total Inventories</h3>
        <p class="text-2xl font-bold">{{ $inventories->count() }}</p>
    </div>
</div>

<!-- KPIs Table -->
<div class="bg-white p-6 mt-6 rounded shadow">
    <h3 class="text-lg font-bold mb-4">KPIs Overview</h3>

    <table class="w-full border border-gray-300">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-4 py-2">KPI Title</th>
                <th class="border px-4 py-2">Current Value</th>
                <th class="border px-4 py-2">Color</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kpis as $kpi)
            <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2">{{ $kpi->title }}</td>
                <td class="border px-4 py-2">{{ $kpi->value }}</td>
                <td class="border px-4 py-2">
                    <span class="inline-block w-4 h-4 rounded" style="background-color: {{ $kpi->color }}"></span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Products Table -->
<div class="bg-white p-6 mt-6 rounded shadow">
    <h3 class="text-lg font-bold mb-4">Products Overview</h3>
    <table class="w-full border border-gray-300">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-4 py-2">Product</th>
                <th class="border px-4 py-2">Price</th>
                <th class="border px-4 py-2">Stock</th>
                <th class="border px-4 py-2">Inventory</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $prod)
            <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2">{{ $prod->pdt_name }}</td>
                <td class="border px-4 py-2">Ksh {{ number_format($prod->price, 2) }}</td>
                <td class="border px-4 py-2">{{ $prod->stock_level }}</td>
                <td class="border px-4 py-2">{{ $prod->inventory->inventory_name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Inventory Table -->
<div class="bg-white p-6 mt-6 rounded shadow">
    <h3 class="text-lg font-bold mb-4">Inventories Overview</h3>
    <table class="w-full border border-gray-300">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-4 py-2">Inventory Name</th>
                <th class="border px-4 py-2">Products Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $inv)
            <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2">{{ $inv->inventory_name }}</td>
                <td class="border px-4 py-2">{{ $inv->products->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
