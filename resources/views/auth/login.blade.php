<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Admin Panel</title>

    {{-- Include Tailwind + custom CSS --}}
    @vite([
        'resources/css/app.css',
        'resources/css/custom.css',
        'resources/js/app.js'
    ])
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 font-sans">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-xl">
        <h2 class="text-3xl font-bold mb-6 text-center text-blue-600">Welcome Back</h2>

        @if($errors->any())
            <div class="mb-4 text-red-600 text-sm text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-gray-700 mb-1">Email</label>
                <input type="email" name="email" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
                       required>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Password</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
                       required>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300 font-semibold">
                Login
            </button>
        </form>

        <p class="mt-6 text-sm text-gray-600 text-center">
            Don’t have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-medium">Register</a>
        </p>
    </div>

</body>
</html>
