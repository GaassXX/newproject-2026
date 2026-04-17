@extends('layout.sidebar')

@section('content')
<div class="mb-6">
    <a href="{{ route('daily-limits.index') }}" class="text-blue-600 hover:text-blue-900">← Back to Daily Limits</a>
    <h1 class="text-3xl font-bold text-gray-900 mt-3">Set Daily Loss Limit</h1>
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

<div class="grid grid-cols-3 gap-6">
    <!-- Form -->
    <div class="col-span-2">
        <x-card title="New Daily Limit">
            <form method="POST" action="{{ route('daily-limits.store') }}">
                @csrf

                <x-input
                    name="date"
                    label="Date"
                    type="date"
                    :value="old('date', date('Y-m-d'))"
                    required
                />

                <div class="grid grid-cols-2 gap-3 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Maximum Loss Allowed</label>
                        <div class="flex gap-2">
                            <input id="max_loss" name="max_loss" type="number" step="0.01" placeholder="e.g., 500.00" value="{{ old('max_loss') }}" class="mt-1 block flex-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-blue-500" required>
                        </div>
                        <p id="conversion_text" class="text-xs text-gray-600 mt-2 font-medium">= -- (disimpan sebagai USD)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Currency</label>
                        <select id="currency" name="currency" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-blue-500 bg-white">
                            <option value="USD">USD (Dollar)</option>
                            <option value="IDR">IDR (Rupiah)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>💡 Tip:</strong> Tetapkan batas kerugian harian maksimal yang masih bisa Anda terima. Jika sudah mencapai batas ini, hentikan trading untuk hari itu.
                    </p>
                </div>

                <div class="flex gap-3">
                    <x-button type="submit">Set Limit</x-button>
                    <a href="{{ route('daily-limits.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </x-card>
    </div>

    <!-- Info Card -->
    <div>
        <x-card title="Why Daily Limits?">
            <div class="space-y-4 text-sm">
                <div>
                    <p class="font-medium text-gray-900">🎯 Risk Management</p>
                    <p class="text-gray-600 mt-1">Lindungi akun Anda dengan membatasi kerugian harian.</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900">🧠 Emotional Control</p>
                    <p class="text-gray-600 mt-1">Berhenti trading ketika batas tercapai untuk menghindari keputusan impulsif.</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900">📊 Consistency</p>
                    <p class="text-gray-600 mt-1">Pertahankan kebiasaan trading yang disiplin dari hari ke hari.</p>
                </div>
                <div class="mt-6 p-3 bg-green-50 border border-green-200 rounded">
                    <p class="text-xs text-green-800 font-medium">
                        ✓ Once limit is reached, your account is locked until the next day.
                    </p>
                </div>
            </div>
        </x-card>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputMaxLoss = document.getElementById('max_loss');
    const selectCurrency = document.getElementById('currency');
    const conversionText = document.getElementById('conversion_text');

    let exchangeRate = null;

    // Fetch current exchange rate dari public API
    async function fetchExchangeRate() {
        try {
            const resp = await fetch('https://api.exchangerate.host/latest?base=USD&symbols=IDR');
            const data = await resp.json();
            if (data.rates && data.rates.IDR) {
                exchangeRate = data.rates.IDR;
                updateConversion();
            }
        } catch (err) {
            console.error('Error fetching exchange rate:', err);
        }
    }

    // Update conversion display
    function updateConversion() {
        if (!inputMaxLoss.value || !exchangeRate) {
            conversionText.textContent = '= -- (disimpan sebagai USD)';
            return;
        }

        const amount = parseFloat(inputMaxLoss.value);
        const currency = selectCurrency.value;

        let converted, displayStr;
        if (currency === 'USD') {
            // Show IDR equivalent for reference
            converted = amount * exchangeRate;
            displayStr = `= Rp ${Number(converted).toLocaleString('id-ID', { maximumFractionDigits: 0 })} (referensi)`;
        } else {
            // Show USD equivalent (what will be stored)
            converted = amount / exchangeRate;
            displayStr = `= $${Number(converted).toLocaleString('en-US', { maximumFractionDigits: 2 })} (disimpan sebagai USD)`;
        }

        conversionText.textContent = displayStr;
    }

    // Listen to input changes
    inputMaxLoss.addEventListener('input', updateConversion);
    selectCurrency.addEventListener('change', updateConversion);

    // Fetch rate on load
    fetchExchangeRate();
});
</script>

@endsection
