@extends('layout.sidebar')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Trading Summary & Recap</h1>
    <p class="text-gray-600 mt-2">Complete analysis of your trading performance with IDR conversion</p>
</div>

<!-- Period Filter -->
<div class="mb-6 flex gap-2">
    @foreach(['7' => '7 Days', '30' => '30 Days', '90' => '90 Days', 'all' => 'All Time'] as $value => $label)
        <a href="{{ route('trading-summary') }}?period={{ $value }}"
           class="px-4 py-2 rounded-lg font-medium transition-colors {{ request()->get('period', '30') == $value ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<!-- Main Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Profit Card -->
    <x-card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Profit (USD)</p>
                <p class="text-2xl font-bold {{ $stats['total_profit_usd'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ${{ number_format($stats['total_profit_usd'], 2) }}
                </p>
                <p class="text-xs text-gray-500 mt-2">IDR: {{ number_format($stats['total_profit_idr'], 0, ',', '.') }}</p>
            </div>
            <svg class="w-8 h-8 {{ $stats['total_profit_usd'] >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
            </svg>
        </div>
    </x-card>

    <!-- Total Trades Card -->
    <x-card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Closed Trades</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_trades'] }}</p>
                <p class="text-xs text-gray-500 mt-2">Open: {{ count($openTrades) }}</p>
            </div>
            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"></path>
            </svg>
        </div>
    </x-card>

    <!-- Win Rate Card -->
    <x-card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Win Rate</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['win_rate'] }}%</p>
                <p class="text-xs text-gray-500 mt-2">{{ $stats['winning_trades'] }}W / {{ $stats['losing_trades'] }}L / {{ $stats['breakeven_trades'] }}B</p>
            </div>
            <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"></path>
            </svg>
        </div>
    </x-card>

    <!-- Profit Factor Card -->
    <x-card>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Profit Factor</p>
                <p class="text-2xl font-bold text-orange-600">
                    @if (is_numeric($stats['profit_factor']))
                        {{ number_format($stats['profit_factor'], 2) }}
                    @else
                        {{ $stats['profit_factor'] }}
                    @endif
                </p>
                <p class="text-xs text-gray-500 mt-2">Wins vs Losses Ratio</p>
            </div>
            <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v2h8v-2zM2 8a2 2 0 11-4 0 2 2 0 014 0zM8 15a4 4 0 00-8 0v2h8v-2z"></path>
            </svg>
        </div>
    </x-card>
</div>

<!-- Secondary Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Average Win/Loss -->
    <x-card title="Average Trade Values">
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-sm text-gray-600">Average Win</span>
                <span class="font-medium text-green-600">${{ number_format($stats['avg_win'], 2) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-sm text-gray-600">Average Loss</span>
                <span class="font-medium text-red-600">${{ number_format($stats['avg_loss'], 2) }}</span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-gray-600">Largest Win</span>
                <span class="font-medium text-green-600">${{ number_format($stats['largest_win'], 2) }}</span>
            </div>
        </div>
    </x-card>

    <!-- Largest Loss -->
    <x-card title="Extreme Values">
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-sm text-gray-600">Largest Loss</span>
                <span class="font-medium text-red-600">${{ number_format($stats['largest_loss'], 2) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-sm text-gray-600">Risk:Reward</span>
                <span class="font-medium text-gray-900">
                    @if (is_numeric($stats['risk_reward']))
                        {{ number_format($stats['risk_reward'], 2) }}
                    @else
                        {{ $stats['risk_reward'] }}
                    @endif
                </span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-gray-600">Consistency</span>
                <span class="font-medium {{ $stats['win_rate'] >= 50 ? 'text-green-600' : 'text-red-600' }}">{{ $stats['win_rate'] >= 50 ? 'Positive' : 'Negative' }}</span>
            </div>
        </div>
    </x-card>

    <!-- Performance by Market -->
    <x-card title="Performance by Market">
        <div class="space-y-2">
            @foreach ($byMarket as $market => $data)
                <div class="py-2 border-b last:border-b-0">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-900 capitalize">{{ $market }}</span>
                        <span class="text-xs font-medium {{ $data['profit_usd'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($data['profit_usd'], 0) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>{{ $data['count'] }} trades</span>
                        <span>{{ $data['win_rate'] }}% WR</span>
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>
</div>

<!-- Daily Performance Chart (Last 30 Days) -->
@if (count($dailyPerformance) > 0)
    <x-card title="Daily Performance (Last 30 Days)">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Date</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Profit (USD)</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Profit (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($dailyPerformance as $date => $profit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
                            <td class="px-4 py-2 text-right {{ $profit >= 0 ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
                                ${{ number_format($profit, 2) }}
                            </td>
                            <td class="px-4 py-2 text-right {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }} text-xs">
                                Rp {{ number_format($profit * 15500, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
@endif

<!-- Recent Trades Table -->
<x-card title="Recent Closed Trades">
    @if ($closedTrades->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Instrument</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Entry</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Exit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Lot</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">P/L USD</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">P/L IDR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($closedTrades->take(20) as $trade)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $trade->instrument }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($trade->market) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $trade->type === 'buy' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($trade->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ number_format($trade->entry_price, $trade->entry_price > 100 ? 0 : 4) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ number_format($trade->exit_price, $trade->exit_price > 100 ? 0 : 4) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ number_format($trade->lot_size, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ ($trade->pnl ?? 0) >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    ${{ number_format($trade->pnl ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ ($trade->pnl ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                Rp {{ number_format(($trade->pnl ?? 0) * 15500, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($closedTrades->count() > 20)
            <div class="mt-4 text-center text-sm text-gray-600">
                Showing 20 of {{ $closedTrades->count() }} trades
            </div>
        @endif
    @else
        <p class="text-center text-gray-500 py-8">No closed trades found for this period.</p>
    @endif
</x-card>

<!-- Note about exchange rate -->
<div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
    <p class="text-sm text-blue-800">
        <strong>💡 Note:</strong> Exchange rate used: 1 USD = Rp 15,500 (This can be updated based on current market rate)
    </p>
</div>
@endsection
