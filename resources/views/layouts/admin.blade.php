<!DOCTYPE html>
<html lang="en">

<head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>@yield('title', 'Admin Dashboard')</title>

       {{-- Load Tailwind CSS + JS via Vite --}}
       @vite(['resources/css/app.css', 'resources/js/app.js'])

       {{-- Optional Google Icons --}}
       <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
</head>

<body class="flex min-h-screen bg-gray-50 font-sans">
       <!-- Sidebar -->
       <aside
              class="w-64 bg-white border-r border-gray-200 shadow-lg fixed md:relative h-full md:h-screen flex flex-col p-6 space-y-6">
              <h2 class="text-2xl font-bold text-blue-600 border-b border-gray-200 pb-3">
                     Dashboard
              </h2>
              <nav class="flex flex-col gap-2">
                     <a href="{{ route('admin.dashboard') }}"
                            class="px-4 py-2 rounded-lg font-medium 
        {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                            📊 Overview
                     </a>

                     <a href="{{ route('admin.inventory') }}"
                            class="px-4 py-2 rounded-lg font-medium 
        {{ request()->routeIs('admin.inventory') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                            📦 Inventory
                     </a>

                     <a href="{{ route('admin.sales') }}"
                            class="px-4 py-2 rounded-lg font-medium 
        {{ request()->routeIs('admin.sales') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                            💰 Sales
                     </a>

                     <a href="{{ route('admin.users') }}"
                            class="px-4 py-2 rounded-lg font-medium 
        {{ request()->routeIs('admin.users') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                            👥 Users
                     </a>

                     <a href="{{ route('admin.kpis') }}"
                            class="px-4 py-2 rounded-lg font-medium 
        {{ request()->routeIs('admin.kpis*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                            📈 KPIs
                     </a>

                     <a href="{{ route('admin.reports') }}"
                            class="px-4 py-2 rounded-lg font-medium 
        {{ request()->routeIs('admin.reports*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                            📑 Reports
                     </a>

                     <a href="{{ route('admin.settings') }}"
                            class="px-4 py-2 rounded-lg font-medium 
        {{ request()->routeIs('admin.settings') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600' }}">
                            ⚙️ Settings
                     </a>
              </nav>

       </aside>

       <!-- Main Content -->
       <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
              <!-- Topbar -->
              <header class="flex justify-between items-center bg-white shadow px-6 py-4 sticky top-0 z-10">
                     <div class="flex gap-4 text-xl text-gray-600">
                            ⚠️ 🔔 👤
                     </div>
                     <div class="text-right">
                            <strong class="block text-gray-800">{{ Auth::user()->name ?? 'Admin' }}</strong>
                            <span class="text-gray-500 text-sm">{{ Auth::user()->role ?? 'Administrator' }}</span>
                     </div>
              </header>

              <!-- Page Content -->
              <main class="p-6 bg-gray-50 flex-1">
                     <h1 class="text-2xl font-bold mb-4">@yield('page-title')</h1>
                     @yield('content')
              </main>
       </div>
</body>

</html>