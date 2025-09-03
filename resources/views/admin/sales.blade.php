@extends('layouts.admin')

@section('content')
<div class="p-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-700">💵 Manage Sales</h1>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Sale ID</th>
                    <th class="p-2 border">Product</th>
                    <th class="p-2 border">Quantity</th>
                    <th class="p-2 border">Amount</th>
                    <th class="p-2 border">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td class="p-2 border">{{ $sale->id }}</td>
                    <td class="p-2 border">{{ $sale->product->name ?? 'N/A' }}</td>
                    <td class="p-2 border">{{ $sale->quantity }}</td>
                    <td class="p-2 border">${{ number_format($sale->amount, 2) }}</td>
                    <td class="p-2 border">{{ $sale->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">No sales records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
