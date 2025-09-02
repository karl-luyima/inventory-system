<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analyst Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="flex bg-gray-100 font-sans">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white h-screen shadow-md">
        <div class="p-4 text-2xl font-bold border-b">Sales Panel</div>
        <nav class="p-4">
            <ul class="space-y-4">
                <li><a href="{{ route('sales.dashboard') }}" class="flex items-center gap-2 hover:text-blue-600">📈 Dashboard</a></li>
                <li><a href="{{ route('sales.reports') }}" class="flex items-center gap-2 hover:text-blue-600">📊 Reports</a></li>
                <li><a href="{{ route('sales.targets') }}" class="flex items-center gap-2 hover:text-blue-600">🎯 Targets</a></li>
                <li><a href="{{ route('sales.clients') }}" class="flex items-center gap-2 hover:text-blue-600">👥 Clients</a></li>
            </ul>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1">
        {{-- Top Bar --}}
        <header class="flex justify-between items-center bg-white shadow px-6 py-4">
            <div class="text-xl font-bold">Sales Analyst Dashboard</div>
            <div class="font-medium text-right">
                {{ Auth::user()->name ?? 'Sales Analyst' }} <br>
                <span class="text-sm text-gray-500">Sales Analyst</span>
            </div>
        </header>

        {{-- Dashboard Content --}}
        <main class="p-6">
            <h1 class="text-2xl font-bold mb-6">Performance Overview</h1>

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700">Monthly Sales</h2>
                    <p class="text-3xl font-bold text-blue-600 mt-2">$24,500</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700">New Clients</h2>
                    <p class="text-3xl font-bold text-green-600 mt-2">18</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700">Sales Growth</h2>
                    <p class="text-3xl font-bold text-purple-600 mt-2">+12%</p>
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4">Monthly Sales Trends</h2>
                    <canvas id="salesTrendChart"></canvas>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4">Top Products</h2>
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    {{-- Chart.js Config --}}
    <script>
        // Sales Trend
        new Chart(document.getElementById('salesTrendChart'), {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun'],
                datasets: [{
                    label: 'Sales ($)',
                    data: [4000, 6000, 8000, 7500, 9000, 12000],
                    borderColor: 'rgb(37,99,235)',
                    backgroundColor: 'rgba(37,99,235,0.2)',
                    fill: true,
                    tension: 0.4
                }]
            }
        });

        // Top Products
        new Chart(document.getElementById('topProductsChart'), {
            type: 'bar',
            data: {
                labels: ['Laptop', 'Shoes', 'Phone', 'TV', 'Watch'],
                datasets: [{
                    label: 'Units Sold',
                    data: [120, 90, 150, 60, 70],
                    backgroundColor: 'rgb(34,197,94)'
                }]
            }
        });
    </script>

</body>
</html>
