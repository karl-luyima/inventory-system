@extends('layouts.inventoryclerk')

@section('page-title', 'Add New Inventory')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Add New Inventory</h2>

    <form action="{{ route('clerk.inventory.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Inventory Name -->
        <div>
            <label for="inventory_name" class="block font-medium text-gray-700">Inventory Name</label>
            <input type="text" name="inventory_name" id="inventory_name" required
                class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Enter Inventory Name">
        </div>

        <!-- Product List (optional) -->
        <div>
            <label for="pdtList" class="block font-medium text-gray-700">Product List (optional)</label>
            <input type="text" name="pdtList" id="pdtList"
                class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder='e.g., ["Laptop","Phone"]'>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit"
                class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">
                Add Inventory
            </button>
        </div>
    </form>
</div>
@endsection
