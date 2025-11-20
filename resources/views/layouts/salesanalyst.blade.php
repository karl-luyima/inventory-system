<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analyst - @yield('page-title', 'Dashboard')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }
        .sidebar-link {
            transition: all 0.2s;
        }
        .sidebar-link:hover {
            background-color: #3f51b5;
        }
    </style>
</head>
<body>
<div class="flex h-screen bg-gray-100">
    
    <!-- Sidebar -->
    <div class="flex-shrink-0 w-64 bg-indigo-800 text-white shadow-xl fixed md:static h-full">
        <div class="p-6 text-2xl font-extrabold border-b border-indigo-700">
            Inventory System
        </div>
        
        <nav class="mt-6 space-y-2 px-4">
            <a href="{{ url('sales/dashboard') }}" 
               class="sidebar-link flex items-center p-3 rounded-lg hover:bg-indigo-700 @if(Request::is('sales/dashboard')) bg-indigo-700 @endif">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2-2m2 2l-7 7m0 0l-7-7m7 7v10m0 0l-7 7"></path>
                </svg>
                Dashboard
            </a>
            <a href="{{ url('sales/reports') }}" 
               class="sidebar-link flex items-center p-3 rounded-lg hover:bg-indigo-700 @if(Request::is('sales/reports')) bg-indigo-700 @endif">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 4h4a2 2 0 002-2v-4a2 2 0 00-2-2h-4a2 2 0 00-2 2v4a2 2 0 002 2z"></path>
                </svg>
                Reports
            </a>
            <a href="{{ route('sales.forecast') }}" 
               class="sidebar-link flex items-center p-3 rounded-lg hover:bg-indigo-700 @if(Request::is('sales/forecast')) bg-indigo-700 @endif">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 7h8m0 0v8m0-8l-8 8m0 0l-4-4m4 4l-4-4"></path>
                </svg>
                Product Predictions
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="flex items-center justify-between p-4 bg-white border-b shadow-md z-10">
            <h1 class="text-2xl font-bold text-gray-800">Sales Analyst</h1>
            <div class="flex items-center space-x-4">
                <!-- Logout POST form -->
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                       class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg shadow hover:bg-red-700 transition">
                       Logout
                    </a>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
