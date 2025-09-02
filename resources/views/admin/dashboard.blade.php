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
<body class="flex bg-gray-100 font-sans">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white h-screen shadow-md">
        <div class="p-4 text-xl font-bold border-b">Admin Panel</div>
        <nav class="p-4">
            <ul class="space-y-4">
                <li><a href="{{ route('admin.users') }}" class="flex items-center gap-2 hover:text-blue-600">👤 Users</a></li>
                <li><a href="{{ route('admin.inventory') }}" class="flex items-center gap-2 hover:text-blue-600">📦 Inventory</a></li>
                <li><a href="{{ route('admin.sales') }}" class="flex items-center gap-2 hover:text-blue-600">💵 Sales</a></li>
                <li><a href="{{ route('admin.reports') }}" class="flex items-center gap-2 hover:text-blue-600">📊 Reports</a></li>
                <li><a href="{{ route('admin.settings') }}" class="flex items-center gap-2 hover:text-blue-600">⚙️ Settings</a></li>
                <li><a href="{{ route('admin.kpis') }}" class="flex items-center gap-2 hover:text-blue-600">📑 KPIs</a></li>
                <li><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 hover:text-blue-600">📈 Dashboard</a></li>
            </ul>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col">
        {{-- Top Bar --}}
        <header class="flex justify-between items-center bg-white shadow p-4">
            <div class="flex items-center gap-4">
                <span>⚠️</span>
                <span>🔔</span>
                <span>👤</span>
            </div>
            <div class="font-medium text-right">
                {{ Auth::user()->name ?? 'Admin' }} <br>
                <span class="text-sm text-gray-500">{{ Auth::user()->role ?? 'Administrator' }}</span>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="p-6 space-y-6">
            <h1 class="text-2xl font-bold mb-4">📊 Dashboard Overview</h1>

            {{-- Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card p-6 bg-white rounded-2xl shadow hover:shadow-lg transition">
                    <h2 class="text-lg font-semibold">Total Users</h2>
                    <p class="text-gray-500">120</p>
                </div>
                <div class="card p-6 bg-white rounded-2xl shadow hover:shadow-lg transition">
                    <h2 class="text-lg font-semibold">Products in Stock</h2>
                    <p class="text-gray-500">340</p>
                </div>
                <div class="card p-6 bg-white rounded-2xl shadow hover:shadow-lg transition">
                    <h2 class="text-lg font-semibold">Monthly Sales</h2>
                    <p class="text-gray-500">$25,000</p>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Sales Chart --}}
                <div class="bg-white p-6 rounded-2xl shadow">
                    <h2 class="text-lg font-semibold mb-4">Monthly Sales Trend</h2>
                    <canvas id="salesChart"></canvas>
                </div>

                {{-- Inventory Chart --}}
                <div class="bg-white p-6 rounded-2xl shadow">
                    <h2 class="text-lg font-semibold mb-4">Stock Levels</h2>
                    <canvas id="inventoryChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    {{-- Chart.js Script --}}
    <script>
        // Sales Line Chart
        const salesCtx = document.getElementById('salesChart');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales ($)',
                    data: [5000, 7000, 8000, 6500, 9000, 12000],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } }
            }
        });

        // Inventory Bar Chart
        const inventoryCtx = document.getElementById('inventoryChart');
        new Chart(inventoryCtx, {
            type: 'bar',
            data: {
                labels: ['Laptops', 'Keyboards', 'Monitors', 'Printers', 'Routers'],
                datasets: [{
                    label: 'Stock Quantity',
                    data: [120, 80, 60, 40, 90],
                    backgroundColor: ['#2563eb', '#64748b', '#f59e0b', '#10b981', '#ef4444'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>
