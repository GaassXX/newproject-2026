@extends('layout.sidebar')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
</div>

@if ($message = Session::get('success'))
    <x-alert type="success">{{ $message }}</x-alert>
@endif

@if ($message = Session::get('error'))
    <x-alert type="error">{{ $message }}</x-alert>
@endif

<div class="grid grid-cols-3 gap-6">
    <!-- Profile Settings -->
    <div class="col-span-2">
        <x-card title="Profile Settings">
            <form method="POST" action="{{ route('settings.update-profile') }}">
                @csrf

                <x-input
                    name="name"
                    label="Full Name"
                    :value="old('name', $user->name)"
                    required
                />

                <x-input
                    name="email"
                    label="Email Address"
                    type="email"
                    :value="old('email', $user->email)"
                    required
                />

                <div class="flex gap-3">
                    <x-button type="submit">Save Changes</x-button>
                </div>
            </form>
        </x-card>

        <!-- Trading Settings -->
        <x-card title="Trading Settings" class="mt-6">
            <form method="POST" action="{{ route('settings.update-trading') }}" id="trading-settings-form">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Preferred Currency</label>
                        <select name="preferred_currency" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-blue-500 bg-white">
                            <option value="USD" {{ old('preferred_currency', $user->preferred_currency ?? 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="IDR" {{ old('preferred_currency', $user->preferred_currency ?? 'USD') == 'IDR' ? 'selected' : '' }}>IDR</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Exchange Rate (1 USD = ? IDR)</label>
                        <input type="number" name="exchange_rate" id="exchange_rate" value="{{ old('exchange_rate', $user->exchange_rate) }}" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-blue-500" placeholder="e.g. 15500" step="0.01">
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-3">
                    <label class="inline-flex items-center">
                        <input type="hidden" name="exchange_rate_auto" value="0">
                        <input type="checkbox" name="exchange_rate_auto" value="1" class="form-checkbox" {{ old('exchange_rate_auto', $user->exchange_rate_auto ?? true) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Update rate automatically</span>
                    </label>

                    <button type="button" id="fetch-rate-btn" class="ml-auto px-3 py-2 bg-gray-800 text-white rounded">Fetch Rate Now</button>
                </div>

                <div class="flex gap-3 mt-4">
                    <x-button type="submit">Save Trading Settings</x-button>
                </div>
            </form>
        </x-card>

        <!-- Password Settings -->
        <x-card title="Change Password" class="mt-6">
            <form method="POST" action="{{ route('settings.update-password') }}">
                @csrf

                <x-input
                    name="current_password"
                    label="Current Password"
                    type="password"
                    required
                />

                <x-input
                    name="password"
                    label="New Password"
                    type="password"
                    required
                />

                <x-input
                    name="password_confirmation"
                    label="Confirm Password"
                    type="password"
                    required
                />

                <div class="flex gap-3">
                    <x-button type="submit">Update Password</x-button>
                </div>
            </form>
        </x-card>
    </div>

    <!-- Account Info -->
    <div>
        <x-card title="Account Information">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">User ID</p>
                    <p class="font-medium text-gray-900">{{ $user->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Member Since</p>
                    <p class="font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Last Updated</p>
                    <p class="font-medium text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-2">Your Roles</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($user->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                            </span>
                        @empty
                            <p class="text-sm text-gray-500">No roles assigned</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('fetch-rate-btn');
    if (!btn) return;
    btn.addEventListener('click', async function () {
        btn.disabled = true;
        btn.textContent = 'Fetching...';
        try {
            const resp = await fetch('{{ route('settings.fetch-rate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });
            const data = await resp.json();
            if (data.ok) {
                const el = document.getElementById('exchange_rate');
                if (el) el.value = data.rate;
                alert('Rate updated: 1 USD = ' + data.rate + ' IDR');
            } else {
                alert('Unable to fetch rate');
            }
        } catch (err) {
            console.error(err);
            alert('Error fetching rate');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Fetch Rate Now';
        }
    });
});
</script>

@endsection
