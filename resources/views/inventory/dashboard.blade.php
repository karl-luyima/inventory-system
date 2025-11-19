@extends('layouts.inventoryclerk')

@section('page-title', 'Inventory Clerk Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Flash Messages -->
    @if(session('success'))
        <div 
            id="flash-message" 
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
            role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div 
            id="flash-message" 
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
            role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <!-- Optional header content -->
    </div>

    <!-- Search Products -->
    <form action="{{ route('clerk.search') }}" method="GET" class="mb-6">
        <input type="text" name="search" placeholder="Search for Product" class="w-full p-3 border rounded-lg">
    </form>

    <!-- Dashboard Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('clerk.metrics') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition block">
            <h3 class="text-lg font-semibold mb-2">View Dashboard Metrics</h3>
            <p class="text-gray-600">See KPIs defined by the admin.</p>
        </a>
    </div>

    <!-- Products Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Products</h2>
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Price</th>
                    <th class="p-2 border">Stock</th>
                    <th class="p-2 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2 border">{{ $product->pdt_id }}</td>
                        <td class="p-2 border">{{ $product->pdt_name }}</td>
                        <td class="p-2 border">{{ $product->price }}</td>
                        <td class="p-2 border">
                            <form action="{{ route('clerk.updateStock', $product->pdt_id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="number" name="stock" value="{{ $product->stock_level }}" class="w-20 p-1 border rounded">
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded">Update</button>
                            </form>
                        </td>
                        <td class="p-2 border">
                            <!-- Optional actions -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Auto-hide flash message script -->
<script>
    setTimeout(() => {
        const flash = document.getElementById('flash-message');
        if(flash){
            flash.style.transition = "opacity 0.5s ease, transform 0.5s ease";
            flash.style.opacity = 0;
            flash.style.transform = "translateY(-20px)";
            setTimeout(() => flash.remove(), 500);
        }
    }, 4000);
</script>

@endsection
