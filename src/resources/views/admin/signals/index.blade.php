@extends('layout.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Signals</h1>

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instrument</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Side</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">TP / SL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Executed</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($signals as $s)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $s->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $s->instrument }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ ucfirst($s->side) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ number_format($s->volume,2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $s->take_profit ?? '-' }} / {{ $s->stop_loss ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $s->status }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $s->executed_at ? $s->executed_at->toDateTimeString() : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $signals->links() }}</div>
</div>
@endsection
