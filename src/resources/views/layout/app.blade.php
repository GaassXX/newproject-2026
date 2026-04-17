<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trading Dashboard')</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation Bar -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">
                            Trading
                        </a>
                    </div>

                    <!-- Main Navigation -->
                    <div class="hidden md:flex space-x-8">
                        <a href="{{ route('dashboard') }}"
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('trades.index') }}"
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('trades.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            Trades
                        </a>
                        <a href="{{ route('daily-limits.index') }}"
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('daily-limits.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            Daily Limits
                        </a>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">
                            {{ auth()->user()->email ?? 'Guest' }}
                        </span>
                        @auth
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Flash Messages -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="font-semibold text-red-800 mb-2">Errors:</div>
                        <ul class="list-disc list-inside text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
                        {{ session('warning') }}
                    </div>
                @endif

                <!-- Page Header -->
                @if (isset($title) || isset($description))
                    <div class="mb-8">
                        @isset($title)
                            <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
                        @endisset
                        @isset($description)
                            <p class="mt-2 text-gray-600">{{ $description }}</p>
                        @endisset
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">About</h3>
                        <p class="text-gray-600">Professional trading management system with real-time monitoring and analytics.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a></li>
                            <li><a href="{{ route('trades.index') }}" class="hover:text-blue-600">Trades</a></li>
                            <li><a href="{{ route('daily-limits.index') }}" class="hover:text-blue-600">Daily Limits</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Support</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li><a href="#" class="hover:text-blue-600">Documentation</a></li>
                            <li><a href="#" class="hover:text-blue-600">Contact Us</a></li>
                            <li><a href="#" class="hover:text-blue-600">FAQ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-8">
                    <p class="text-center text-gray-600">&copy; {{ date('Y') }} Trading Dashboard. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
