@extends('layout.sidebar')

@section('title', $trade ? 'Edit Trade' : 'Create Trade')

@section('content')
    <div class="max-w-2xl">
        <x-card :title="$trade ? 'Edit Trade' : 'Create New Trade'">
            <form action="{{ $trade ? route('trades.update', $trade) : route('trades.store') }}" method="POST">
                @csrf
                @if ($trade)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Instrument -->
                    <x-input
                        name="instrument"
                        label="Instrument"
                        placeholder="e.g., EUR/USD, AAPL, BTC/USDT"
                        value="{{ $trade->instrument ?? '' }}"
                        required
                    />

                    <!-- Market Type -->
                    <x-select
                        name="market"
                        label="Market"
                        :options="['forex' => 'Forex', 'crypto' => 'Crypto', 'saham' => 'Saham']"
                        value="{{ $trade->market ?? '' }}"
                        required
                    />

                    <!-- Trade Type -->
                    <x-select
                        name="type"
                        label="Trade Type"
                        :options="['buy' => 'Buy', 'sell' => 'Sell']"
                        value="{{ $trade->type ?? '' }}"
                        required
                    />

                    <!-- Status -->
                    <x-select
                        name="status"
                        label="Status"
                        :options="['open' => 'Open', 'closed' => 'Closed', 'cancelled' => 'Cancelled']"
                        value="{{ $trade->status ?? 'open' }}"
                        required
                    />

                    <!-- Entry Price -->
                    <x-input
                        name="entry_price"
                        label="Entry Price"
                        type="number"
                        step="0.001"
                        value="{{ $trade->entry_price ?? '' }}"
                        required
                    />

                    <!-- Exit Price (show jika status = closed) -->
                    <x-input
                        name="exit_price"
                        label="Exit Price"
                        type="number"
                        step="0.001"
                        value="{{ $trade->exit_price ?? '' }}"
                        required="{{ old('status', $trade->status ?? 'open') === 'closed' }}"
                    />

                    <!-- Stop Loss -->
                    <x-input
                        name="stop_loss"
                        label="Stop Loss"
                        type="number"
                        step="0.001"
                        value="{{ $trade->stop_loss ?? '' }}"
                    />

                    <!-- Take Profit -->
                    <x-input
                        name="take_profit"
                        label="Take Profit"
                        type="number"
                        step="0.001"
                        value="{{ $trade->take_profit ?? '' }}"
                    />

                    <!-- Lot Size -->
                    <x-input
                        name="lot_size"
                        label="Lot Size"
                        type="number"
                        step="0.01"
                        value="{{ $trade->lot_size ?? '' }}"
                        required
                    />

                    <!-- Opened At -->
                    <x-input
                        name="opened_at"
                        label="Opened At"
                        type="datetime-local"
                        value="{{ $trade ? $trade->opened_at?->format('Y-m-d\TH:i') : '' }}"
                        required
                    />
                </div>

                <!-- Info P/L untuk closed trade -->
                <div id="pnl-info" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                    <p class="text-sm text-blue-800">
                        <strong>💡 P/L otomatis dihitung:</strong> (Exit Price - Entry Price) × Lot Size
                        <br>
                        Untuk SELL: (Entry Price - Exit Price) × Lot Size
                    </p>
                </div>

                    <!-- Closed At -->
                    @if ($trade || old('status') === 'closed')
                        <x-input
                            name="closed_at"
                            label="Closed At"
                            type="datetime-local"
                            value="{{ $trade ? $trade->closed_at?->format('Y-m-d\TH:i') : old('closed_at') }}"
                        />
                    @endif

                </div>

                <!-- Strategy -->
                <x-input
                    name="strategy"
                    label="Strategy"
                    placeholder="e.g., Scalping, Swing, Position"
                    value="{{ $trade->strategy ?? '' }}"
                />

                <!-- Notes -->
                <x-textarea
                    name="notes"
                    label="Notes"
                    placeholder="Add trading notes..."
                    value="{{ $trade->notes ?? '' }}"
                    :rows="4"
                />

                <div class="flex gap-3 mt-6">
                    <x-button type="submit" variant="primary">
                        {{ $trade ? 'Update Trade' : 'Create Trade' }}
                    </x-button>
                    <a href="{{ route('trades.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        // Toggle PNL info berdasarkan status
        const statusSelect = document.querySelector('select[name="status"]');
        const pnlInfo = document.getElementById('pnl-info');

        function togglePnlInfo() {
            if (statusSelect && pnlInfo) {
                if (statusSelect.value === 'closed') {
                    pnlInfo.classList.remove('hidden');
                } else {
                    pnlInfo.classList.add('hidden');
                }
            }
        }

        if (statusSelect) {
            togglePnlInfo();
            statusSelect.addEventListener('change', togglePnlInfo);
        }
    </script>
@endsection
