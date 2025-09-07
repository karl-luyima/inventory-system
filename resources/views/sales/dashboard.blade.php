@extends('layouts.salesanalyst')

@section('title', 'Sales Analyst Dashboard')
@section('page-title', '📊 Sales Analyst Dashboard')

@section('content')
    <!-- Record New Sale -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h2 class="text-lg font-semibold mb-4">➕ Record New Sale</h2>
        <form id="saleForm" class="space-y-4">
            @csrf
            <div>
                <label for="pdt_id" class="block text-sm font-medium text-gray-700">Product ID</label>
                <input type="text" name="pdt_id" id="pdt_id"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" id="quantity"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                <input type="number" step="0.01" name="amount" id="amount"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
            </div>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                💾 Save Sale
            </button>
        </form>
        <p id="saleMessage" class="text-green-600 font-medium mt-3 hidden"></p>
    </div>

    <!-- Top Performing Products -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h2 class="text-lg font-semibold mb-4">🔥 Top Performing Products</h2>
        <ul id="topProducts" class="space-y-2">
            @foreach ($topProducts as $item)
                <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                    <span>{{ $item->product->pdt_name ?? 'Unknown Product' }}</span>
                    <span class="text-gray-600">Sold: {{ $item->total_sold }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Latest Sales -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">🕒 Latest Sales</h2>
        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Product</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Quantity</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Amount</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Date</th>
                </tr>
            </thead>
            <tbody id="salesTable" class="divide-y divide-gray-200">
                @foreach ($sales as $sale)
                    <tr>
                        <td class="px-4 py-2">{{ $sale->product->pdt_name ?? 'Unknown' }}</td>
                        <td class="px-4 py-2">{{ $sale->quantity }}</td>
                        <td class="px-4 py-2">Ksh {{ number_format($sale->totalAmount, 2) }}</td>
                        <td class="px-4 py-2">{{ $sale->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- AJAX Script --}}
    <script>
        document.getElementById('saleForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            fetch("{{ route('sales.store') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    document.getElementById('saleMessage').innerText = data.message;
                    document.getElementById('saleMessage').classList.remove('hidden');

                    // Reset form
                    document.getElementById('saleForm').reset();

                    // Update Top Products
                    let productsHTML = '';
                    data.topProducts.forEach(item => {
                        productsHTML += `
                            <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                <span>${item.product?.pdt_name ?? 'Unknown Product'}</span>
                                <span class="text-gray-600">Sold: ${item.total_sold}</span>
                            </li>
                        `;
                    });
                    document.getElementById('topProducts').innerHTML = productsHTML;

                    // Update Latest Sales
                    let salesHTML = '';
                    data.sales.forEach(sale => {
                        salesHTML += `
                            <tr>
                                <td class="px-4 py-2">${sale.product?.pdt_name ?? 'Unknown'}</td>
                                <td class="px-4 py-2">${sale.quantity}</td>
                                <td class="px-4 py-2">Ksh ${parseFloat(sale.amount).toFixed(2)}</td>
                                <td class="px-4 py-2">${new Date(sale.created_at).toLocaleString()}</td>
                            </tr>
                        `;
                    });
                    document.getElementById('salesTable').innerHTML = salesHTML;
                }
            })
            .catch(err => console.error(err));
        });
    </script>
@endsection
