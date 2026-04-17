@extends('layout.auth')

@section('title', 'Register')

@section('content')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Create Account</h2>

        @if ($errors->any())
            <x-alert type="error" class="mb-6">
                <p class="font-semibold mb-2">Registration failed:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <x-input
                name="name"
                label="Full Name"
                placeholder="John Doe"
                value="{{ old('name') }}"
                required
            />

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

            <x-input
                name="password_confirmation"
                type="password"
                label="Confirm Password"
                placeholder="••••••••"
                required
            />

            <label class="flex items-center">
                <input type="checkbox" name="agree_terms" class="w-4 h-4 text-blue-600 rounded" required>
                <span class="ml-2 text-sm text-gray-700">
                    I agree to the
                    <a href="#" class="text-blue-600 hover:text-blue-800">Terms of Service</a>
                </span>
            </label>

            <x-button type="submit" variant="primary" class="w-full justify-center">
                Create Account
            </x-button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-700 text-sm">
                Already have an account?
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Sign in here
                </a>
            </p>
        </div>
    </div>
@endsection
