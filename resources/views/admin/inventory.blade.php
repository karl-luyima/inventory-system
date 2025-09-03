@extends('layouts.admin')

@section('content')
<div class="p-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-700">📦 Manage Inventory</h1>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Product ID</th>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Stock</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory as $item)
                <tr>
                    <td class="p-2 border">{{ $item->id }}</td>
                    <td class="p-2 border">{{ $item->name }}</td>
                    <td class="p-2 border">{{ $item->stock }}</td>
                    <td class="p-2 border">
                        <a href="{{ route('inventory.edit', $item->id) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</a>
                        <form action="{{ route('inventory.delete', $item->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $inventory->links() }}
        </div>
    </div>
</div>
@endsection
