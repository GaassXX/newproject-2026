@extends('layout.auth')

@section('title', 'Login')

@section('content')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Sign In</h2>

        @if ($errors->any())
            <x-alert type="error" class="mb-6">
                <p class="font-semibold mb-2">Login failed:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <x-input
                name="email"
                type="email"
                label="Email Address"
                placeholder="you@example.com"
                value="{{ old('email') }}"
                required
            />

            <x-input
                name="password"
                type="password"
                label="Password"
                placeholder="••••••••"
                required
            />

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded">
                    <span class="ml-2 text-sm text-gray-700">Remember me</span>
                </label>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                    Forgot password?
                </a>
            </div>

            <x-button type="submit" variant="primary" class="w-full justify-center">
                Sign In
            </x-button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-700 text-sm">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Sign up here
                </a>
            </p>
        </div>
    </div>
@endsection
