<body class="flex bg-gray-100">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="p-6 text-2xl font-bold border-b text-primary">Admin Panel</div>
        <nav class="p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="active">📈 Dashboard</a>
            <a href="{{ route('admin.users') }}">👤 Users</a>
            <a href="{{ route('admin.inventory') }}">📦 Inventory</a>
            <a href="{{ route('admin.sales') }}">💵 Sales</a>
            <a href="{{ route('admin.reports') }}">📊 Reports</a>
            <a href="{{ route('admin.settings') }}">⚙️ Settings</a>
            <a href="{{ route('admin.kpis') }}">📑 KPIs</a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1">
        <header class="topbar">
            <div class="flex gap-4 text-xl">
                ⚠️ 🔔 👤
            </div>
            <div class="profile">
                <strong>{{ Auth::user()->name ?? 'Admin' }}</strong><br>
                <span>{{ Auth::user()->role ?? 'Administrator' }}</span>
            </div>
        </header>

        <main class="p-6">
            @yield('content')
        </main>
    </div>
</body>
