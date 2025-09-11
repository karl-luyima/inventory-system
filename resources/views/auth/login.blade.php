<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 font-sans">

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-3xl font-extrabold text-center text-blue-600 mb-2">Welcome Back 👋</h2>
        <p class="text-center text-gray-600 mb-6">Login to access your dashboard</p>

        <!-- Success message -->
        @if(session('success'))
            <div class="mb-4 text-green-600 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                <div class="relative">
                    <input id="email" type="email" name="email" placeholder="Enter your email"
                           value="{{ old('email') }}"
                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
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
                    <input id="password" type="password" name="password" placeholder="Enter your password"
                           class="w-full pl-10 pr-10 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                           required>
                    <i data-lucide="lock" class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"></i>
                    <button type="button" 
                            onclick="togglePassword()" 
                            class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <i id="eyeIcon" data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror

                <!-- Forgot Password -->
                <div class="text-right mt-1">
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                        Forgot password?
                    </a>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember"
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                <label for="remember" class="ml-2 text-gray-600 text-sm">Remember me</label>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300 font-semibold shadow-md">
                Login
            </button>
        </form>

        <p class="mt-6 text-sm text-gray-600 text-center">
            Don’t have an account?
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-medium">Register</a>
        </p>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            if (password.type === "password") {
                password.type = "text";
                eyeIcon.setAttribute("data-lucide", "eye-off");
            } else {
                password.type = "password";
                eyeIcon.setAttribute("data-lucide", "eye");
            }

            lucide.createIcons(); // re-render icons after toggle
        }

        lucide.createIcons(); // render all icons on page load
    </script>

</body>
</html>
