<?php

namespace App\Http\Controllers;

use App\Models\DailyLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DailyLimitController extends Controller
{
    public function index()
    {
        /** @var int|null $userId */
        $userId = Auth::id();

        $limits = DailyLimit::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->paginate(10);

        $today = DailyLimit::where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->first();

        return view('daily-limits.index', compact('limits', 'today'));
    }

    public function create()
    {
        return view('daily-limits.create');
    }

    /**
     * Fetch current USD to IDR exchange rate
     */
    private function getExchangeRate()
    {
        try {
            $resp = Http::timeout(5)->get('https://api.exchangerate.host/latest', [
                'base' => 'USD',
                'symbols' => 'IDR',
            ])->throw();

            return data_get($resp->json(), 'rates.IDR', 15500); // fallback to reasonable rate
        } catch (\Exception $e) {
            // Fallback ke rate dari user atau default
            return auth()->user()->exchange_rate ?? 15500;
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'max_loss' => 'required|numeric|min:1',
            'date'     => 'required|date',
            'currency' => 'required|in:USD,IDR',
        ]);

        $amount = (float) $request->max_loss;
        $currency = $request->currency;

        // Convert IDR to USD untuk disimpan di database (normalized ke USD)
        $amountInUSD = $amount;
        if ($currency === 'IDR') {
            $exchangeRate = $this->getExchangeRate();
            $amountInUSD = $amount / $exchangeRate;
        }

        $limit = DailyLimit::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'date'    => $request->date,
            ],
            [
                'max_loss'     => round($amountInUSD, 2),
                'currency'     => 'USD', // always store as USD
                'is_locked'    => false,
                'current_loss' => 0,
            ]
        );

        if (! $limit->wasRecentlyCreated) {
            $limit->update([
                'max_loss' => round($amountInUSD, 2),
                'currency' => 'USD',
            ]);
        }

        return redirect()->route('daily-limits.index')
            ->with('success', 'Daily limit berhasil diset!');
    }

    public function update(Request $request, DailyLimit $dailyLimit)
    {
        abort_if($dailyLimit->user_id !== Auth::id(), 403);

        $dailyLimit->update([
            'is_locked' => $request->boolean('is_locked'),
        ]);

        return redirect()->route('daily-limits.index')
            ->with('success', 'Status berhasil diupdate!');
    }

    public function destroy(DailyLimit $dailyLimit)
    {
        abort_if($dailyLimit->user_id !== Auth::id(), 403);
        $dailyLimit->delete();

        return redirect()->route('daily-limits.index')
            ->with('success', 'Limit berhasil dihapus!');
    }
}
