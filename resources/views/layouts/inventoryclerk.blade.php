<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventory Clerk Dashboard')</title>
    @vite('resources/css/app.css')

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-64 bg-green-700 text-white flex flex-col p-4 space-y-6 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out z-20 fixed md:relative h-full">
            
            <h2 class="text-2xl font-bold mb-8 flex items-center gap-2">
                <i data-lucide="boxes"></i> Inventory Clerk
            </h2>

            <nav class="flex flex-col gap-2">
                <a href="{{ route('clerk.dashboard') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded 
                    {{ request()->routeIs('clerk.dashboard') 
                        ? 'bg-green-900 text-white font-semibold' 
                        : 'hover:bg-green-600' }}">
                    <i data-lucide="layout-dashboard"></i> Dashboard
                </a>

                <a href="{{ route('clerk.metrics') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded 
                    {{ request()->routeIs('clerk.metrics') 
                        ? 'bg-green-900 text-white font-semibold' 
                        : 'hover:bg-green-600' }}">
                    <i data-lucide="bar-chart-3"></i> View Dashboard Metrics
                </a>
            </nav>

            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST" class="mt-auto"
                onsubmit="return confirm('Are you sure you want to logout?')">
                @csrf
                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 px-3 py-2 rounded flex items-center gap-2 justify-center">
                    <i data-lucide="log-out"></i> Logout
                </button>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-y-auto md:ml-64">
            <!-- Topbar -->
            <header class="flex justify-between items-center mb-6 sticky top-0 bg-gray-100 z-10 py-2">
                <!-- Mobile Toggle -->
                <button id="sidebarToggle" class="md:hidden text-gray-700">
                    <i data-lucide="menu"></i>
                </button>

                <!-- Page Title -->
                <h1 class="text-2xl font-bold">@yield('page-title', 'Inventory Clerk Dashboard')</h1>

                <!-- User Info -->
                <div class="text-right">
                    <p class="font-semibold">{{ session('name') }}</p>
                    <p class="text-sm text-gray-600">{{ ucfirst(session('role')) }}</p>
                </div>
            </header>

            <!-- Page Section -->
            <section>
                @yield('content')
            </section>
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
