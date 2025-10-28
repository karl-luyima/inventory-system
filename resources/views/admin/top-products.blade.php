@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-md">
    <h2 class="text-lg font-semibold mb-4">Top Products</h2>

    <table class="w-full border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border-b text-left">Product</th>
                <th class="px-4 py-2 border-b text-left">Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $product)
            <tr>
                <td class="px-4 py-2 border-b">{{ $product->name }}</td>
                <td class="px-4 py-2 border-b">{{ $product->sales }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
