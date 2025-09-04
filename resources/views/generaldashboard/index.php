@extends('layouts.admin')

@section('title', 'General Dashboard')
@section('page-title', '📊 General Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-md text-center">
        <h2 class="text-lg font-semibold text-gray-700">Total Products</h2>
        <p id="totalProducts" class="text-3xl font-bold text-blue-600 mt-2">0</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md text-center">
        <h2 class="text-lg font-semibold text-gray-700">Total Inventory</h2>
        <p id="totalInventory" class="text-3xl font-bold text-green-600 mt-2">0</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md text-center">
        <h2 class="text-lg font-semibold text-gray-700">Total Sales</h2>
        <p id="totalSales" class="text-3xl font-bold text-purple-600 mt-2">$0</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md text-center">
        <h2 class="text-lg font-semibold text-gray-700">Predicted Inventory</h2>
        <p id="predictedInventory" class="text-3xl font-bold text-orange-600 mt-2">0</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Product Performance</h2>
        <canvas id="productChart"></canvas>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Inventory Forecast</h2>
        <canvas id="inventoryChart"></canvas>
    </div>
</div>
@endsection
