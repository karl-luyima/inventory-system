<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white shadow-md h-screen sticky top-0">
        <div class="p-6 text-xl font-bold text-blue-600">
            Company Dashboard
        </div>
        <nav class="mt-6">
            <ul class="space-y-2">

                {{-- General Dashboard (Shared) --}}
                <li>
                    <a href="{{ route('general.dashboard') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        📈 General Dashboard
                    </a>
                </li>

                {{-- Admin Links --}}
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        🏠 Admin Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        👥 Users
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.inventory') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        📦 Inventory
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.sales') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        💰 Sales
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        📑 Reports
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kpis') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        🎯 KPIs
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        ⚙️ Settings
                    </a>
                </li>

                {{-- Inventory Clerk --}}
                <li class="mt-6">
                    <a href="{{ route('clerk.dashboard') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        📝 Clerk Dashboard
                    </a>
                </li>

                {{-- Sales Analyst --}}
                <li>
                    <a href="{{ route('analyst.dashboard') }}"
                       class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-100">
                        📊 Analyst Dashboard
                    </a>
                </li>

            </ul>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 p-6">
        <header class="mb-6 border-b pb-4">
            <h1 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
        </header>
        <section>
            @yield('content')
        </section>
    </main>

</body>
</html>
