@extends('layout.sidebar')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Role Management</h1>
    <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
        + Create Role
    </a>
</div>

@if ($message = Session::get('success'))
    <x-alert type="success">{{ $message }}</x-alert>
@endif

@if ($message = Session::get('error'))
    <x-alert type="error">{{ $message }}</x-alert>
@endif

<x-card title="All Roles">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($roles as $role)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $role->description ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $role->users_count ?? 0 }} users
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="text-gray-600">{{ $role->permissions_count ?? 0 }} perms</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                        <a href="{{ route('roles.edit', $role) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                            Edit
                        </a>
                        @unless(in_array($role->name, ['super-admin', 'admin']))
                            <form method="POST" action="{{ route('roles.destroy', $role) }}" class="inline-block"
                                  onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                            </form>
                        @endunless
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No roles found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($roles->hasPages())
        <div class="mt-6 border-t pt-4">
            {{ $roles->links() }}
        </div>
    @endif
</x-card>
@endsection
