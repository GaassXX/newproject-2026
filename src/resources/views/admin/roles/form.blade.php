@extends('layout.sidebar')

@section('content')
<div class="mb-6">
    <a href="{{ route('roles.index') }}" class="text-blue-600 hover:text-blue-900">← Back to Roles</a>
    <h1 class="text-3xl font-bold text-gray-900 mt-3">
        @isset($role)
            Edit Role
        @else
            Create New Role
        @endisset
    </h1>
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

<x-card title="{{ isset($role) ? 'Edit Role' : 'New Role' }}">
    <form method="POST" action="@isset($role){{ route('roles.update', $role) }}@else{{ route('roles.store') }}@endisset">
        @csrf
        @isset($role)
            @method('PUT')
        @endisset

        <x-input
            name="name"
            label="Role Name"
            placeholder="e.g., trader, manager"
            :value="old('name', $role->name ?? '')"
            required
        />

        <x-textarea
            name="description"
            label="Description (optional)"
            rows="3"
            placeholder="What is this role for?"
        >{{ old('description', $role->description ?? '') }}</x-textarea>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Assign Permissions</label>

            @if ($permissions->count() > 0)
                <div class="space-y-6">
                    @foreach ($permissions as $module => $perms)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <input
                                    type="checkbox"
                                    id="select_all_{{ $module }}"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    onchange="selectModule(this, '{{ $module }}')"
                                />
                                <label for="select_all_{{ $module }}" class="ml-3 font-medium text-gray-900 cursor-pointer capitalize">
                                    {{ ucfirst(str_replace('_', ' ', $module)) }}
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-3 ml-7">
                                @foreach ($perms as $permission)
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            id="perm_{{ $permission->id }}"
                                            data-module="{{ $module }}"
                                            @checked(in_array($permission->id, old('permissions', $rolePermissions ?? [])))
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            onchange="updateSelectAll()"
                                        />
                                        <label for="perm_{{ $permission->id }}" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                            {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No permissions available.</p>
            @endif
        </div>

        <div class="flex gap-3">
            <x-button type="submit">
                @isset($role)
                    Update Role
                @else
                    Create Role
                @endisset
            </x-button>
            <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
        </div>
    </form>
</x-card>

<script>
function selectModule(checkbox, module) {
    const perms = document.querySelectorAll(`input[data-module="${module}"]`);
    perms.forEach(perm => {
        perm.checked = checkbox.checked;
    });
    updateSelectAll();
}

function updateSelectAll() {
    const modules = new Set();
    document.querySelectorAll('input[data-module]').forEach(el => {
        modules.add(el.dataset.module);
    });

    modules.forEach(module => {
        const perms = document.querySelectorAll(`input[data-module="${module}"]`);
        const allChecked = Array.from(perms).every(p => p.checked);
        const anyChecked = Array.from(perms).some(p => p.checked);

        const selectAll = document.getElementById(`select_all_${module}`);
        if (selectAll) {
            selectAll.checked = allChecked;
            selectAll.indeterminate = anyChecked && !allChecked;
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateSelectAll);
</script>
@endsection
