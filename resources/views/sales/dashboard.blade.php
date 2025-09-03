<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Analyst Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">

        <!-- Top Bar -->
        <header class="flex justify-between items-center bg-white shadow px-6 py-4">
            <h1 class="text-xl font-semibold text-gray-700">💵 Sales Analyst</h1>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-bold">
                    {{ strtoupper(substr(Auth::user()->user_id, 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium">{{ Auth::user()->user_id }}</p>
                    <p class="text-sm text-gray-500">Sales Analyst</p>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-8 space-y-8">

            <!-- Add Sale -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold">➕ Add New Sale</h2>
                <form method="POST" action="{{ route('analyst.store') }}" class="space-y-4">
                    @csrf
                    <input type="text" name="pdt_id" placeholder="Product ID" class="w-full px-3 py-2 border rounded-lg">
                    <input type="number" name="quantity" placeholder="Quantity" class="w-full px-3 py-2 border rounded-lg">
                    <input type="number" step="0.01" name="amount" placeholder="Amount" class="w-full px-3 py-2 border rounded-lg">

                    <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700">
                        Record Sale
                    </button>
                </form>
            </div>

            <!-- Reports -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-2">🏆 Top Performing Products</h2>
                    <ul class="list-disc ml-6 text-gray-700">
                        @foreach($topProducts as $product)
                            <li>{{ $product->name }} - Stock: {{ $product->stock }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-2">📜 Recent Transactions</h2>
                    <ul class="list-disc ml-6 text-gray-700">
                        @foreach($sales as $sale)
                            <li>Product {{ $sale->pdt_id }} - Qty: {{ $sale->quantity }} - ${{ $sale->amount }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
