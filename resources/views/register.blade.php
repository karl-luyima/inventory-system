<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-full max-w-sm bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-center text-green-600">Register</h2>

        @if($errors->any())
            <div class="mb-4 text-red-600 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-700">User ID</label>
                <input type="text" name="user_id" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-green-200" required>
            </div>
            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-green-200" required>
            </div>
            <div>
                <label class="block text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-green-200" required>
            </div>
            <div>
                <label class="block text-gray-700">Role</label>
                <select name="role" class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-green-200" required>
                    <option value="">-- Select Role --</option>
                    <option value="administrator">Administrator</option>
                    <option value="inventory_clerk">Inventory Clerk</option>
                    <option value="sales_analyst">Sales Analyst</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                Register
            </button>
        </form>

        <p class="mt-6 text-sm text-gray-600 text-center">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-green-600 hover:underline">Login</a>
        </p>
    </div>
</body>
</html>
