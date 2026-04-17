<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication')</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-gradient-to-br from-blue-600 to-blue-800">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white">Trading</h1>
                <p class="text-blue-100 mt-2">Professional Trading Management System</p>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-lg shadow-xl p-8">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-blue-100 text-sm">
                    &copy; {{ date('Y') }} Trading Dashboard. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
