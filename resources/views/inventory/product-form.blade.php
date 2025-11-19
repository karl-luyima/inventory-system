@extends('layouts.inventoryclerk')

@section('page-title', 'Add New Product')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Add New Product</h2>

    <!-- Flash Messages -->
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

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clerk.products.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Product Name -->
        <div>
            <label for="pdt_name" class="block font-medium text-gray-700">Product Name</label>
            <input type="text" name="pdt_name" id="pdt_name" value="{{ old('pdt_name') }}" required
                class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Enter Product Name">
        </div>

        <!-- Price -->
        <div>
            <label for="price" class="block font-medium text-gray-700">Price</label>
            <input type="number" name="price" id="price" step="0.01" value="{{ old('price') }}" required
                class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Enter Price">
        </div>

        <!-- Stock Level -->
        <div>
            <label for="stock_level" class="block font-medium text-gray-700">Stock Level</label>
            <input type="number" name="stock_level" id="stock_level" value="{{ old('stock_level') }}" required
                class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Enter Stock Level">
        </div>

        <!-- Inventory Selection -->
        <div>
            <label for="inventory_id" class="block font-medium text-gray-700">Inventory</label>
            <select name="inventory_id" id="inventory_id" required
                class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">-- Select Inventory --</option>
                @foreach($inventories as $inventory)
                    <option value="{{ $inventory->inventory_id }}" {{ old('inventory_id') == $inventory->inventory_id ? 'selected' : '' }}>
                        {{ $inventory->inventory_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit"
                class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">
                Add Product
            </button>
        </div>
    </form>
</div>
@endsection
