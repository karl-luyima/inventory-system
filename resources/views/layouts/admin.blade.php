<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>

    {{-- Load Tailwind CSS + JS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-1/5 bg-gray-100 p-6 space-y-4 shadow-md">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Dashboard</h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="block px-4 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-blue-100' }}">
                        📊 Overview
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventory.index') }}"
                        class="block px-4 py-2 rounded {{ request()->routeIs('inventory.*') ? 'bg-blue-600 text-white' : 'hover:bg-blue-100' }}">
                        📦 Inventory
                    </a>
                </li>
                <li>
                    <a href="{{ route('sales.index') }}"
                        class="block px-4 py-2 rounded {{ request()->routeIs('sales.*') ? 'bg-blue-600 text-white' : 'hover:bg-blue-100' }}">
                        💰 Sales
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}"
                        class="block px-4 py-2 rounded {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'hover:bg-blue-100' }}">
                        👥 Users
                    </a>
                </li>
                <li>
                    <a href="{{ route('kpi.index') }}"
                        class="block px-4 py-2 rounded {{ request()->routeIs('kpi.*') ? 'bg-blue-600 text-white' : 'hover:bg-blue-100' }}">
                        📈 KPIs
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.index') }}"
                        class="block px-4 py-2 rounded {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white' : 'hover:bg-blue-100' }}">
                        📑 Reports
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.index') }}"
                        class="block px-4 py-2 rounded {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white' : 'hover:bg-blue-100' }}">
                        ⚙️ Settings
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8 space-y-6">
            <h1 class="text-2xl font-bold">@yield('page-title')</h1>
            @yield('content')
        </div>
    </div>
</body>

</html>
