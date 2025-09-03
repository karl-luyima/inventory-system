<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Admin Panel</title>

    {{-- Include Tailwind + custom CSS --}}
    @vite([
        'resources/css/app.css',
        'resources/css/custom.css',
        'resources/js/app.js'
    ])
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 font-sans">

    <div class="w-full max-w-md bg-white p-10 rounded-2xl shadow-xl">
        <h2 class="text-4xl font-extrabold mb-6 text-center text-green-600">Create Account</h2>

        {{-- Display validation errors --}}
        @if($errors->any())
            <div class="mb-4 text-red-600 text-sm text-center">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Registration Form --}}
        <form method="POST" action="{{ route('register.submit') }}" class="space-y-5">
            @csrf

            <!-- Full Name -->
            <div>
                <label class="block text-gray-600 mb-1">Full Name</label>
                <input type="text" name="name"
                       class="w-full px-5 py-3 border rounded-xl focus:outline-none focus:ring-4 focus:ring-green-300"
                       placeholder="Enter your full name"
                       required>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-gray-600 mb-1">Email</label>
                <input type="email" name="email"
                       class="w-full px-5 py-3 border rounded-xl focus:outline-none focus:ring-4 focus:ring-green-300"
                       placeholder="Enter your email"
                       required>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-gray-600 mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full px-5 py-3 border rounded-xl focus:outline-none focus:ring-4 focus:ring-green-300"
                       placeholder="Enter password"
                       required>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-gray-600 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-5 py-3 border rounded-xl focus:outline-none focus:ring-4 focus:ring-green-300"
                       placeholder="Confirm password"
                       required>
            </div>

            <!-- Role -->
            <div>
                <label class="block text-gray-600 mb-1">Role</label>
                <select name="role"
                        class="w-full px-5 py-3 border rounded-xl focus:outline-none focus:ring-4 focus:ring-green-300"
                        required>
                    <option value="">-- Select Role --</option>
                    <option value="administrator">Administrator</option>
                    <option value="inventory_clerk">Inventory Clerk</option>
                    <option value="sales_analyst">Sales Analyst</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition duration-300 font-semibold shadow-md hover:scale-105">
                Register
            </button>
        </form>

        <!-- Login Link -->
        <p class="mt-6 text-sm text-gray-500 text-center">
            Already have an account?
            <a href="{{ route('login') }}" class="text-green-600 hover:underline font-medium">Login</a>
        </p>
    </div>

</body>
</html>
