<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sales Analyst Dashboard')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-900">

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-purple-700 text-white flex flex-col p-4">
            <h2 class="text-2xl font-bold mb-8">Sales Analyst</h2>
            <nav class="flex flex-col gap-4">
                <a href="{{ route('sales.dashboard') }}" class="hover:bg-purple-600 px-3 py-2 rounded">Dashboard</a>
                
                <a href="{{ route('sales.reports') }}" class="hover:bg-purple-600 px-3 py-2 rounded">Reports</a>
            </nav>
            <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                @csrf
                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 px-3 py-2 rounded">Logout</button>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-y-auto">
            <header class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">@yield('page-title', 'Sales Analyst Dashboard')</h1>
                <div class="text-right">
                    <p class="font-semibold">{{ session('name') }}</p> <!-- ✅ now consistent -->
                    <p class="text-sm text-gray-600">{{ ucfirst(session('role')) }}</p>
                </div>
            </header>

            <section>
                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
