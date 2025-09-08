<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventory Clerk Dashboard')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 text-gray-900">

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-green-700 text-white flex flex-col p-4">
            <h2 class="text-2xl font-bold mb-8">Inventory Clerk</h2>
            <nav class="flex flex-col gap-4">
                <a href="{{ route('clerk.dashboard') }}" class="hover:bg-green-600 px-3 py-2 rounded">Dashboard</a>
                <a href="{{ route('clerk.metrics') }}" class="hover:bg-green-600 px-3 py-2 rounded">View Dashboard Metrics</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                @csrf
                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 px-3 py-2 rounded">Logout</button>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-y-auto">
            <header class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">@yield('page-title', 'Inventory Clerk Dashboard')</h1>
                <div class="text-right">
                    <p class="font-semibold">{{ session('name') }}</p>
                    <p class="text-sm text-gray-200">{{ ucfirst(session('role')) }}</p>
                </div>
            </header>

            <section>
                @yield('content')
            </section>
        </main>
    </div>
</body>

</html>