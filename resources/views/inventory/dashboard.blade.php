<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Clerk Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="flex bg-gray-100 font-sans">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white h-screen shadow-md">
        <div class="p-4 text-2xl font-bold border-b">Inventory Panel</div>
        <nav class="p-4">
            <ul class="space-y-4">
                <li><a href="{{ route('inventory.dashboard') }}" class="flex items-center gap-2 hover:text-blue-600">📦 Dashboard</a></li>
                <li><a href="{{ route('inventory.stock') }}" class="flex items-center gap-2 hover:text-blue-600">📊 Stock Levels</a></li>
                <li><a href="{{ route('inventory.reorders') }}" class="flex items-center gap-2 hover:text-blue-600">🔄 Reorders</a></li>
                <li><a href="{{ route('inventory.suppliers') }}" class="flex items-center gap-2 hover:text-blue-600">🏭 Suppliers</a></li>
            </ul>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1">
        {{-- Top Bar --}}
        <header class="flex justify-between items-center bg-white shadow px-6 py-4">
            <div class="text-xl font-bold">Inventory Clerk Dashboard</div>
            <div class="font-medium text-right">
                {{ Auth::user()->name ?? 'Inventory Clerk' }} <br>
                <span class="text-sm text-gray-500">Inventory Clerk</span>
            </div>
        </header>

        {{-- Dashboard Content --}}
        <main class="p-6">
            <h1 class="text-2xl font-bold mb-6">Stock Overview</h1>

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700">Total Stock</h2>
                    <p class="text-3xl font-bold text-blue-600 mt-2">5,230</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700">Low Stock Items</h2>
                    <p class="text-3xl font-bold text-red-600 mt-2">12</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700">Pending Reorders</h2>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">5</p>
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4">Stock Categories</h2>
                    <canvas id="stockCategoryChart"></canvas>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4">Reorder Levels</h2>
                    <canvas id="reorderChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    {{-- Chart.js Config --}}
    <script>
        // Stock Categories
        new Chart(document.getElementById('stockCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: ['Electronics', 'Clothing', 'Food', 'Furniture'],
                datasets: [{
                    label: 'Items',
                    data: [1200, 800, 2000, 1230],
                    backgroundColor: [
                        'rgb(37,99,235)',
                        'rgb(234,179,8)',
                        'rgb(34,197,94)',
                        'rgb(239,68,68)'
                    ]
                }]
            }
        });

        // Reorder Levels
        new Chart(document.getElementById('reorderChart'), {
            type: 'bar',
            data: {
                labels: ['Laptops', 'T-Shirts', 'Rice', 'Chairs'],
                datasets: [{
                    label: 'Units Needed',
                    data: [40, 30, 60, 20],
                    backgroundColor: 'rgb(239,68,68)'
                }]
            }
        });
    </script>

</body>
</html>
