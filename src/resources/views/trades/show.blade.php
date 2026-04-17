@extends('layout.sidebar')

@section('title', 'Trade Details')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Trade Info -->
        <div class="lg:col-span-2">
            <x-card title="Trade Information">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Instrument</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $trade->instrument }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Market</p>
                        <p class="text-lg font-semibold">
                            <span class="px-3 py-1 rounded-full text-xs {{ match($trade->market) {
                                'crypto' => 'bg-orange-100 text-orange-800',
                                'forex' => 'bg-blue-100 text-blue-800',
                                'saham' => 'bg-green-100 text-green-800',
                                default => 'bg-gray-100 text-gray-800'
                            } }}">
                                {{ ucfirst($trade->market) }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Type</p>
                        <p class="text-lg font-semibold">
                            <span class="px-3 py-1 rounded-full text-sm {{ $trade->type === 'buy' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($trade->type) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p class="text-lg font-semibold">
                            <span class="px-3 py-1 rounded-full text-sm {{ $trade->status === 'closed' ? 'bg-gray-100 text-gray-800' : ($trade->status === 'cancelled' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($trade->status) }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Entry Price</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($trade->entry_price, 3) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Exit Price</p>
                        <p class="text-lg font-bold text-gray-900">{{ $trade->exit_price ? '$' . number_format($trade->exit_price, 3) : '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Stop Loss</p>
                        <p class="text-lg font-bold text-gray-900">{{ $trade->stop_loss ? '$' . number_format($trade->stop_loss, 3) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Take Profit</p>
                        <p class="text-lg font-bold text-gray-900">{{ $trade->take_profit ? '$' . number_format($trade->take_profit, 3) : '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Lot Size</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($trade->lot_size, 2) }}</p>
                    </div>
                </div>

                @if ($trade->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">Notes</p>
                        <p class="text-gray-900">{{ $trade->notes }}</p>
                    </div>
                @endif

                @if ($trade->strategy)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">Strategy</p>
                        <p class="font-medium text-gray-900">{{ $trade->strategy }}</p>
                    </div>
                @endif
            </x-card>

            <!-- Profit/Loss Analysis -->
            <x-card title="Analysis" class="mt-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Risk</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">
                            @php
                                $risk = $trade->stop_loss ? abs($trade->entry_price - $trade->stop_loss) : 0;
                            @endphp
                            ${{ number_format($risk, 3) }}
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Reward</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">
                            @php
                                $reward = $trade->take_profit ? abs($trade->take_profit - $trade->entry_price) : 0;
                            @endphp
                            ${{ number_format($reward, 3) }}
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Risk/Reward Ratio</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $trade->risk_reward ? number_format($trade->risk_reward, 2) : '-' }}</p>
                    </div>
                    <div class="p-4 {{ ($trade->pnl ?? 0) >= 0 ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                        <p class="text-sm {{ ($trade->pnl ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">Profit/Loss</p>
                        <p class="text-lg font-bold {{ ($trade->pnl ?? 0) >= 0 ? 'text-green-900' : 'text-red-900' }} mt-1">
                            ${{ number_format($trade->pnl ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Sidebar Info -->
        <div>
            <!-- Quick Stats -->
            <x-card title="Details">
                <div class="space-y-4">
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-xs text-gray-600 uppercase">Created</p>
                        <p class="text-sm font-medium text-gray-900">{{ $trade->created_at->diffForHumans() }}</p>
                        <p class="text-xs text-gray-500">{{ $trade->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    @if ($trade->updated_at->ne($trade->created_at))
                        <div class="pb-4 border-b border-gray-200">
                            <p class="text-xs text-gray-600 uppercase">Last Updated</p>
                            <p class="text-sm font-medium text-gray-900">{{ $trade->updated_at->diffForHumans() }}</p>
                            <p class="text-xs text-gray-500">{{ $trade->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    @endif

                    @if ($trade->status === 'closed')
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Closed</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $trade->closed_at ? $trade->closed_at->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Actions -->
            <div class="mt-6 space-y-2">
                @if ($trade->status === 'open')
                    <a href="{{ route('trades.edit', $trade) }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center font-medium">
                        Edit Trade
                    </a>
                    <form action="{{ route('trades.close', $trade) }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium" onclick="return confirm('Close this trade?')">
                            Close Trade
                        </button>
                    </form>
                @endif

                <form action="{{ route('trades.destroy', $trade) }}" method="POST" class="block" onsubmit="return confirm('Delete this trade?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        Delete Trade
                    </button>
                </form>

                <a href="{{ route('trades.index') }}" class="block w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center font-medium">
                    Back to Trades
                </a>
            </div>
        </div>
    </div>
@endsection
