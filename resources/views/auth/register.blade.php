<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 font-sans">

    <div class="w-full max-w-md bg-white p-10 rounded-2xl shadow-lg">
        <h2 class="text-3xl font-extrabold mb-2 text-center text-green-600">Create Account</h2>
        <p class="text-center text-gray-600 mb-6">Fill in your details to get started</p>

        <!-- Display all errors -->
        @if($errors->any())
            <div class="mb-4 text-red-600 text-sm text-center">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Success message -->
        @if(session('success'))
            <div class="mb-4 text-green-600 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" class="space-y-5">
            @csrf

            <!-- Full Name -->
            <div>
                <label for="name" class="block text-gray-700 font-medium mb-1">Full Name</label>
                <input id="name" type="text" name="name" placeholder="Enter your full name"
                       value="{{ old('name') }}"
                       class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-400"
                       required>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                <input id="email" type="email" name="email" placeholder="Enter your email address"
                       value="{{ old('email') }}"
                       class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-400"
                       required>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                <input id="password" type="password" name="password" placeholder="Create a password"
                       class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-400"
                       required>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium mb-1">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm your password"
                       class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-400"
                       required>
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-gray-700 font-medium mb-1">Role</label>
                <select id="role" name="role"
                        class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-400"
                        required>
                    <option value="">-- Select Role --</option>
                    <option value="administrator" {{ old('role') == 'administrator' ? 'selected' : '' }}>Administrator</option>
                    <option value="inventory_clerk" {{ old('role') == 'inventory_clerk' ? 'selected' : '' }}>Inventory Clerk</option>
                    <option value="sales_analyst" {{ old('role') == 'sales_analyst' ? 'selected' : '' }}>Sales Analyst</option>
                </select>
            </div>

            <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition duration-300 font-semibold shadow-md hover:scale-105">
                Register
            </button>
        </form>

        <p class="mt-6 text-sm text-gray-600 text-center">
            Already have an account?
            <a href="{{ route('login') }}" class="text-green-600 hover:underline font-medium">Login</a>
        </p>
    </div>

</body>
</html>
