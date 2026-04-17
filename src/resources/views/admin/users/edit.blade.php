@extends('layout.sidebar')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-900">← Back to Users</a>
    <h1 class="text-3xl font-bold text-gray-900 mt-3">Edit User</h1>
</div>

@if ($errors->any())
    <x-alert type="error">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

@if ($message = Session::get('success'))
    <x-alert type="success">{{ $message }}</x-alert>
@endif

<div class="grid grid-cols-3 gap-6">
    <!-- User Info Card -->
    <div class="col-span-2">
        <x-card title="User Information">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

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

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Roles</label>
                    <div class="space-y-3">
                        @forelse ($roles as $role)
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="roles[]"
                                    value="{{ $role->id }}"
                                    id="role_{{ $role->id }}"
                                    @checked(in_array($role->id, old('roles', $userRoles ?? [])))
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="role_{{ $role->id }}" class="ml-3 block text-sm text-gray-700 cursor-pointer">
                                    <span class="font-medium">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</span>
                                    @if ($role->description)
                                        <span class="text-gray-500 ml-1">- {{ $role->description }}</span>
                                    @endif
                                </label>
                            </div>
                        @empty
                            <p class="text-gray-500">No roles available.</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex gap-3">
                    <x-button type="submit">Update User</x-button>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </x-card>
    </div>

    <!-- User Meta Card -->
    <div>
        <x-card title="User Information">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">ID</p>
                    <p class="font-medium text-gray-900">{{ $user->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Created</p>
                    <p class="font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Last Updated</p>
                    <p class="font-medium text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Current Roles</p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @forelse ($user->roles as $role)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
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
@endsection
