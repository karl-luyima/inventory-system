<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-full max-w-sm bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Login</h2>
        
        @if($errors->any())
            <div class="mb-4 text-red-600 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-700">User ID</label>
                <input type="text" name="user_id" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Login
            </button>
        </form>

        <p class="mt-6 text-sm text-gray-600 text-center">
            Don’t have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
        </p>
    </div>
</body>
</html>
