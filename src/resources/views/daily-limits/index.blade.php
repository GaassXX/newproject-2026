@extends('layout.sidebar')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Daily Loss Limits</h1>
    <a href="{{ route('daily-limits.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
        + Set New Limit
    </a>
</div>

@if ($message = Session::get('success'))
    <x-alert type="success">{{ $message }}</x-alert>
@endif

@if ($message = Session::get('error'))
    <x-alert type="error">{{ $message }}</x-alert>
@endif

<!-- Today's Limit Card -->
@if ($today)
    <x-card title="Today's Limit" class="mb-6">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-600">Max Loss Allowed</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($today->max_loss, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Current Loss</p>
                <p class="text-2xl font-bold {{ $today->current_loss > 0 ? 'text-red-600' : 'text-green-600' }}">
                    -${{ number_format($today->current_loss, 2) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Remaining</p>
                <p class="text-2xl font-bold {{ ($today->max_loss - $today->current_loss) < 0 ? 'text-red-600' : 'text-green-600' }}">
                    ${{ number_format($today->max_loss - $today->current_loss, 2) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <div class="mt-3">
                    @if ($today->is_locked)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            🔒 Locked
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✓ Active
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-6">
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Loss Progress</span>
                <span class="text-sm font-medium text-gray-700">{{ number_format(($today->current_loss / $today->max_loss) * 100, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-red-600 h-3 rounded-full transition-all" style="width: {{ min(($today->current_loss / $today->max_loss) * 100, 100) }}%"></div>
            </div>
        </div>
    </x-card>
@endif

<!-- All Daily Limits -->
<x-card title="All Daily Limits">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Loss</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Loss</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($limits as $limit)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $limit->date->format('M d, Y') }}
                        @if ($limit->date->isToday())
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Today
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        ${{ number_format($limit->max_loss, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $limit->current_loss > 0 ? 'text-red-600' : 'text-green-600' }}">
                        -${{ number_format($limit->current_loss, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <span class="{{ ($limit->max_loss - $limit->current_loss) < 0 ? 'text-red-600' : 'text-green-600' }}">
                            ${{ number_format($limit->max_loss - $limit->current_loss, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <form method="POST" action="{{ route('daily-limits.update', $limit) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_locked" value="{{ $limit->is_locked ? 0 : 1 }}">
                            <button type="submit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors
                                {{ $limit->is_locked ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                {{ $limit->is_locked ? '🔒 Locked' : '✓ Active' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ min(($limit->current_loss / $limit->max_loss) * 100, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format(($limit->current_loss / $limit->max_loss) * 100, 0) }}%</p>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                        <form method="POST" action="{{ route('daily-limits.destroy', $limit) }}" class="inline-block"
                              onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">No daily limits set yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($limits->hasPages())
        <div class="mt-6 border-t pt-4">
            {{ $limits->links() }}
        </div>
    @endif
</x-card>
@endsection
