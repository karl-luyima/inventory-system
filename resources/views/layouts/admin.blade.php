<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <h1 class="text-xl font-semibold text-gray-700">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                        {{ strtoupper(substr(Auth::user()->user_id ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ Auth::user()->user_id ?? 'Admin' }}</p>
                        <p class="text-sm text-gray-500">Administrator</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1">
            @yield('content')
        </main>
    </div>

</body>
</html>
