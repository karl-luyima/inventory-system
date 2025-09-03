<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen bg-gray-100 font-sans">

    {{-- Sidebar --}}
    <aside class="sidebar w-64 bg-white shadow-lg flex flex-col fixed md:relative h-full md:h-screen md:flex-shrink-0 transition-transform transform -translate-x-full md:translate-x-0 md:static">
        <div class="p-6 text-2xl font-bold border-b border-gray-200 text-blue-600">
            Admin Panel
        </div>
        <nav class="p-4 flex-1 flex flex-col gap-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-white bg-blue-600">
                📈 Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-gray-700 hover:bg-blue-100 hover:text-blue-600">
                👤 Users
            </a>
            <a href="{{ route('admin.inventory') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-gray-700 hover:bg-blue-100 hover:text-blue-600">
                📦 Inventory
            </a>
            <a href="{{ route('admin.sales') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-gray-700 hover:bg-blue-100 hover:text-blue-600">
                💵 Sales
            </a>
            <a href="{{ route('admin.reports') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-gray-700 hover:bg-blue-100 hover:text-blue-600">
                📊 Reports
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-gray-700 hover:bg-blue-100 hover:text-blue-600">
                ⚙️ Settings
            </a>
            <a href="{{ route('admin.kpis') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-gray-700 hover:bg-blue-100 hover:text-blue-600">
                📑 KPIs
            </a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        {{-- Topbar --}}
        <header class="flex justify-between items-center bg-white shadow px-6 py-4 sticky top-0 z-10">
            <div class="flex gap-4 text-xl text-gray-600">
                ⚠️ 🔔 👤
            </div>
            <div class="text-right">
                <strong class="block text-gray-800">{{ Auth::user()->name ?? 'Admin' }}</strong>
                <span class="text-gray-500 text-sm">{{ Auth::user()->role ?? 'Administrator' }}</span>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="p-6 bg-gray-50 flex-1">
            @yield('content')
        </main>
    </div>

    {{-- Optional Responsive Sidebar Toggle Script --}}
    <script>
        const sidebar = document.querySelector('.sidebar');
        // You can add toggle functionality for small screens here if needed
    </script>
</body>
</html>
