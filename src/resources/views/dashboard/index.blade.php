@extends('layout.sidebar')

@section('title', 'Dashboard')

@push('header-actions')
    <a href="{{ route('trades.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        New Trade
    </a>
@endpush

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Closed Trades Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Closed Trades</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalTrades ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total P/L Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total P/L</p>
                    <p class="text-3xl font-bold {{ ($totalPnl ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                        ${{ number_format($totalPnl ?? 0, 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-lg {{ ($totalPnl ?? 0) >= 0 ? 'bg-green-50' : 'bg-red-50' }} flex items-center justify-center">
                    <svg class="w-6 h-6 {{ ($totalPnl ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V9.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 8H12z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Win Rate Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Win Rate</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $winRate ?? 0 }}%</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $winTrades ?? 0 }}/{{ $totalTrades ?? 0 }} wins</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Open Trades Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Open Trades</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $openTrades ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-orange-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a1 1 0 001 1h12a1 1 0 001-1V6a2 2 0 00-2-2H4zm0 6v-1h10v1H4zm10 3H4a2 2 0 00-2 2v2a2 2 0 002 2h8a2 2 0 002-2v-2a2 2 0 00-2-2z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- 30 Days Cumulative P/L Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">30 Days Cumulative P/L</h3>
            @if (count($chartLabels ?? []) > 0)
                <canvas id="performanceChart" class="h-64"></canvas>
            @else
                <div class="h-64 bg-gray-50 rounded flex items-center justify-center">
                    <p class="text-gray-500">No closed trades in the last 30 days</p>
                </div>
            @endif
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
            <div class="space-y-4">
                <div class="pb-4 border-b border-gray-200">
                    <p class="text-xs text-gray-600 uppercase">Win Trades</p>
                    <p class="text-2xl font-bold text-green-600">{{ $winTrades ?? 0 }}</p>
                </div>
                <div class="pb-4 border-b border-gray-200">
                    <p class="text-xs text-gray-600 uppercase">Loss Trades</p>
                    <p class="text-2xl font-bold text-red-600">{{ $lossTrades ?? 0 }}</p>
                </div>
                <div class="pb-4 border-b border-gray-200">
                    <p class="text-xs text-gray-600 uppercase">Avg Risk/Reward</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $avgRR ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 uppercase">Daily Limit</p>
                    <p class="text-sm text-gray-900 mt-2">
                        @if ($dailyLimit)
                            {{ $dailyLimit->is_locked ? '🔒 LOCKED' : '✓ Active' }}
                        @else
                            <span class="text-gray-500">No limit set</span>
                        @endif
                    </p>
                    @if ($dailyLimit)
                        <div class="mt-2">
                            <div class="text-xs text-gray-600">Loss: ${{ number_format($dailyLimit->current_loss ?? 0, 2) }} / ${{ number_format($dailyLimit->max_loss ?? 0, 2) }}</div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                @php
                                    $percentage = $dailyLimit->max_loss > 0 ? ($dailyLimit->current_loss / $dailyLimit->max_loss) * 100 : 0;
                                @endphp
                                <div class="bg-red-600 h-2 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Trades Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Trades</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Symbol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Entry</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Exit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Profit/Loss</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($recentTrades ?? [] as $trade)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $trade->instrument }}</span>
                                    <span class="ml-2 text-xs text-gray-500">{{ ucfirst($trade->market) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                ${{ number_format($trade->entry_price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $trade->exit_price ? '$' . number_format($trade->exit_price, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ ($trade->pnl ?? 0) >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    ${{ number_format($trade->pnl ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $trade->status === 'closed' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($trade->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('trades.show', $trade) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No trades found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        @if (count($chartLabels ?? []) > 0)
            const ctx = document.getElementById('performanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels ?? []) !!},
                    datasets: [{
                        label: 'Cumulative P/L ($)',
                        data: {!! json_encode($chartData ?? []) !!},
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        @endif
    </script>
@endpush

