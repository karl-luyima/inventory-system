<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 font-sans">

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-3xl font-extrabold text-center text-green-600 mb-2">Create Account</h2>
        <p class="text-center text-gray-600 mb-6">Fill in your details to get started</p>

        <!-- Success Message -->
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
                <div class="relative">
                    <input id="name" type="text" name="name"
                           value="{{ old('name') }}"
                           placeholder="Enter your full name"
                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                           required>
                    <i data-lucide="user" class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"></i>
                </div>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                <div class="relative">
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="Enter your email address"
                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                           required>
                    <i data-lucide="mail" class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"></i>
                </div>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password"
                           placeholder="Create a password"
                           class="w-full pl-10 pr-10 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                           required>
                    <i data-lucide="lock" class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"></i>
                    <button type="button" onclick="togglePassword('password','eyeIcon1')"
                            class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <i id="eyeIcon1" data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium mb-1">Confirm Password</label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           placeholder="Confirm your password"
                           class="w-full pl-10 pr-10 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                           required>
                    <i data-lucide="lock" class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"></i>
                    <button type="button" onclick="togglePassword('password_confirmation','eyeIcon2')"
                            class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <i id="eyeIcon2" data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-gray-700 font-medium mb-1">Role</label>
                <div class="relative">
                    <select id="role" name="role"
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                            required>
                        <option value="">-- Select Role --</option>
                        <option value="administrator" {{ old('role') == 'administrator' ? 'selected' : '' }}>Administrator</option>
                        <option value="inventory_clerk" {{ old('role') == 'inventory_clerk' ? 'selected' : '' }}>Inventory Clerk</option>
                        <option value="sales_analyst" {{ old('role') == 'sales_analyst' ? 'selected' : '' }}>Sales Analyst</option>
                    </select>
                    <i data-lucide="shield" class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"></i>
                </div>
                @error('role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition duration-300 font-semibold shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                Register
            </button>
        </form>

        <p class="mt-6 text-sm text-gray-600 text-center">
            Already have an account?
            <a href="{{ route('login') }}" class="text-green-600 hover:underline font-medium">Login</a>
        </p>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.setAttribute("data-lucide", "eye-off");
            } else {
                input.type = "password";
                icon.setAttribute("data-lucide", "eye");
            }

            lucide.createIcons();
        }

        lucide.createIcons();
    </script>

</body>
</html>
