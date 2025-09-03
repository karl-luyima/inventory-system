<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Clerk Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">

        <!-- Top Bar -->
        <header class="flex justify-between items-center bg-white shadow px-6 py-4">
            <h1 class="text-xl font-semibold text-gray-700">📦 Inventory Clerk</h1>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold">
                    {{ strtoupper(substr(Auth::user()->user_id, 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium">{{ Auth::user()->user_id }}</p>
                    <p class="text-sm text-gray-500">Inventory Clerk</p>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-8 space-y-8">
            
            <!-- Search -->
            <form method="GET" action="{{ route('clerk.search') }}" class="bg-white p-6 rounded-lg shadow-md flex gap-2">
                <input type="text" name="search" placeholder="Search for product..." 
                       class="flex-1 px-3 py-2 border rounded-lg focus:ring focus:ring-green-200">
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Search</button>
            </form>

            <!-- Products Table -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">📋 Product List</h2>
                <table class="w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">ID</th>
                            <th class="p-2 border">Name</th>
                            <th class="p-2 border">Stock</th>
                            <th class="p-2 border">Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td class="p-2 border">{{ $product->pdt_id }}</td>
                            <td class="p-2 border">{{ $product->name }}</td>
                            <td class="p-2 border">{{ $product->stock }}</td>
                            <td class="p-2 border">
                                <form method="POST" action="{{ route('clerk.updateStock', $product->pdt_id) }}" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="stock" value="{{ $product->stock }}" class="w-20 px-2 border rounded">
                                    <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Save</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
