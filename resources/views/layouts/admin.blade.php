<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>

    {{-- Tailwind via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="flex min-h-screen bg-gray-50 font-sans">
    <!-- Sidebar -->
    <aside id="sidebar"
        class="w-64 bg-white border-r border-gray-200 shadow-lg fixed md:relative h-full md:h-screen flex flex-col p-6 space-y-6 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out z-30">
        <h2 class="text-2xl font-bold text-blue-600 border-b border-gray-200 pb-3 flex items-center gap-2">
            <i data-lucide="layout-dashboard"></i> Dashboard
        </h2>
        <nav class="flex flex-col gap-2 mt-4">
            <a href="{{ route('admin.inventory') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium
                {{ request()->routeIs('admin.inventory') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                <i data-lucide="package"></i> Inventory
            </a>

            <a href="{{ route('admin.users') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium
                {{ request()->routeIs('admin.users') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                <i data-lucide="users"></i> Users
            </a>
            <a href="{{ route('admin.kpis') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium
                {{ request()->routeIs('admin.kpis*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                <i data-lucide="bar-chart-3"></i> KPIs
            </a>
            <a href="{{ route('admin.reports') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium
                {{ request()->routeIs('admin.reports*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                <i data-lucide="file-text"></i> Reports
            </a>
            <a href="{{ route('admin.settings') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium
                {{ request()->routeIs('admin.settings') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                <i data-lucide="settings"></i> Settings
            </a>

            <a href="{{ route('admin.topProducts') }}"
                class="flex items-center gap-2 px-3 py-2 rounded
                {{ request()->routeIs('admin.topProducts') 
                    ? 'bg-green-900 text-white font-semibold' 
                    : 'hover:bg-green-600' }}">
                <i data-lucide="trending-up"></i> Top Products
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="flex justify-between items-center bg-white shadow px-6 py-4 sticky top-0 z-20">
            <!-- Sidebar Toggle (Mobile) -->
            <button id="sidebarToggle" class="md:hidden text-gray-600" aria-label="Toggle sidebar">
                <i data-lucide="menu"></i>
            </button>

            <!-- User Info + Logout -->
            <div class="flex items-center gap-6 ml-auto flex-wrap">
                <!-- User Info -->
                <div class="text-right">
                    <span class="block text-gray-800 font-semibold">
                        Welcome {{ ucwords(session('name') ?? 'Admin') }}
                    </span>
                    <span class="text-gray-500 text-sm">
                        {{ ucfirst(session('role') ?? 'Administrator') }}
                    </span>
                </div>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Are you sure you want to logout?')">
                    @csrf
                    <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6 bg-gray-50 flex-1">
            <div class="max-w-5xl mx-auto">
                <h1 class="text-2xl font-bold mb-4">@yield('page-title')</h1>
                <div class="space-y-6">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle').addEventListener('click', () => {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });

        // Load Lucide icons
        lucide.createIcons();
    </script>
</body>

</html>