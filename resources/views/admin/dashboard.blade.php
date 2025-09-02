<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Inventory System</title>
    
    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="flex bg-gray-50 font-sans">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white h-screen shadow-lg">
        <div class="p-6 text-2xl font-bold text-blue-600 border-b">
            InventoryPro
        </div>
        <nav class="p-6">
            <ul class="space-y-4 text-gray-700 font-medium">
                <li><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">📈 Dashboard</a></li>
                <li><a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">👤 Users</a></li>
                <li><a href="{{ route('admin.inventory') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">📦 Inventory</a></li>
                <li><a href="{{ route('admin.sales') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">💵 Sales</a></li>
                <li><a href="{{ route('admin.reports') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">📊 Reports</a></li>
                <li><a href="{{ route('admin.kpis') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">📑 KPIs</a></li>
                <li><a href="{{ route('admin.settings') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">⚙️ Settings</a></li>
            </ul>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col">
        
        {{-- Top Bar --}}
        <header class="flex justify-between items-center bg-white shadow px-6 py-4">
            <h1 class="text-xl font-semibold text-gray-700">📊 Dashboard</h1>
            <div class="flex items-center gap-6">
                <button class="relative">
                    🔔
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">3</span>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-sm text-gray-500">{{ Auth::user()->role ?? 'Administrator' }}</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="p-8 space-y-8">
            
            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-2xl shadow-lg">
                    <h2 class="text-lg font-semibold">Total Users</h2>
                    <p class="text-3xl font-bold mt-2">{{ $usersCount }}</p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-2xl shadow-lg">
                    <h2 class="text-lg font-semibold">Products in Stock</h2>
                    <p class="text-3xl font-bold mt-2">{{ $productsCount }}</p>
                </div>
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-2xl shadow-lg">
                    <h2 class="text-lg font-semibold">Monthly Sales</h2>
                    <p class="text-3xl font-bold mt-2">${{ number_format($monthlySales, 2) }}</p>
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <h2 class="text-lg font-semibold mb-4">Monthly Sales Trend</h2>
                    <canvas id="salesChart"></canvas>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <h2 class="text-lg font-semibold mb-4">Stock Levels</h2>
                    <canvas id="inventoryChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    {{-- Chart.js --}}
    <script>
        // Sales Line Chart
        const salesCtx = document.getElementById('salesChart');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($salesMonths), // e.g. ["Jan","Feb","Mar"]
                datasets: [{
                    label: 'Sales ($)',
                    data: @json($salesData), // e.g. [5000,7000,8000]
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true }
        });

        // Inventory Bar Chart
        const inventoryCtx = document.getElementById('inventoryChart');
        new Chart(inventoryCtx, {
            type: 'bar',
            data: {
                labels: @json($productNames), // e.g. ["Laptops","Keyboards"]
                datasets: [{
                    label: 'Stock Quantity',
                    data: @json($productStock), // e.g. [120,80,60]
                    backgroundColor: ['#2563eb','#16a34a','#f59e0b','#dc2626','#9333ea']
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>
