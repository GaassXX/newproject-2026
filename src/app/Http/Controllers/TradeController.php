<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\DailyLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TradeController extends Controller
{
    public function index()
    {
        $query = Trade::where('user_id', Auth::id());

        // Search by instrument or notes
        if ($search = request('search')) {
            $normalized = preg_replace('/[^A-Za-z0-9]/', '', $search);
            $upper = strtoupper($search);
            $normalizedUpper = strtoupper($normalized);

            $query->where(function ($q) use ($search, $upper, $normalizedUpper) {
                // match instrument raw, case-insensitive, or normalized (remove / _ etc)
                $q->whereRaw("UPPER(instrument) LIKE ?", ["%{$upper}%"])
                  ->orWhereRaw("REPLACE(REPLACE(UPPER(instrument), '/', ''), '_', '') LIKE ?", ["%{$normalizedUpper}%"])
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter by status (open/closed/cancelled)
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // Optional sorting
        $sort = request('sort', 'opened_at');
        $direction = request('direction', 'desc');

        $trades = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        return view('trades.index', compact('trades'));
    }

    public function create()
    {
        $trade = null;
        return view('trades.form', compact('trade'));
    }

    public function store(Request $request)
    {
        $status = $request->input('status');

        // Validation rules
        $rules = [
            'instrument'  => 'required|string',
            'type'        => 'required|in:buy,sell',
            'market'      => 'required|in:crypto,forex,saham',
            'entry_price' => 'required|numeric',
            'lot_size'    => 'required|numeric',
            'opened_at'   => 'required|date',
            'status'      => 'required|in:open,closed,cancelled',
            'stop_loss'   => 'nullable|numeric',
            'take_profit' => 'nullable|numeric',
        ];

        // Jika status closed, exit_price wajib
        if ($status === 'closed') {
            $rules['exit_price'] = 'required|numeric';
            $rules['closed_at'] = 'nullable|date';
        } else {
            $rules['exit_price'] = 'nullable|numeric';
        }

        $request->validate($rules);

        // Cek daily limit (runtime check respects user's preferred currency)
        $dailyLimit = DailyLimit::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        if ($dailyLimit && $dailyLimit->is_locked) {
            return back()->withErrors([
                'limit' => 'Trading hari ini dikunci! Kamu sudah mencapai batas loss harian.'
            ])->withInput();
        }

        $data            = $request->all();
        $data['user_id'] = Auth::id();

        // Hitung RR otomatis
        if ($request->stop_loss && $request->take_profit) {
            $risk   = abs($request->entry_price - $request->stop_loss);
            $reward = abs($request->take_profit - $request->entry_price);
            $data['risk_reward'] = $risk > 0 ? round($reward / $risk, 2) : null;
        }

        // Hitung PNL otomatis jika status closed dan ada exit_price
        if ($status === 'closed' && $request->exit_price) {
            $priceDiff = $request->exit_price - $request->entry_price;
            if ($request->type === 'sell') {
                $priceDiff = $request->entry_price - $request->exit_price;
            }

            // Tentukan contract size / units per standard lot per market/instrument
            $instrument = strtoupper($request->instrument ?? '');
            $market = $request->market;

            // Default contract sizes
            $contractSize = 1; // default for stocks/crypto

            // Special rules
            if (str_contains($instrument, 'XAU') || str_contains($instrument, 'GOLD')) {
                // XAUUSD (Gold) typical contract = 100 troy ounces per lot
                $contractSize = 100;
            } elseif ($market === 'forex') {
                // standard forex lot = 100,000 units
                $contractSize = 100000;
            }

            // PNL = price difference * lot_size * contractSize
            $data['pnl'] = round($priceDiff * ($request->lot_size ?? 0) * $contractSize, 2);

            // Set closed_at jika belum ada
            if (!$request->closed_at) {
                $data['closed_at'] = now();
            }
        }

        $trade = Trade::create($data);

        // Update current loss di daily limit
        if ($dailyLimit && isset($data['pnl']) && $data['pnl'] < 0) {
            $user = auth()->user();

            $newLossUSD = $dailyLimit->current_loss + abs($data['pnl']);

            // Runtime: check in user's preferred currency (e.g., IDR) using exchange_rate
            if (($user->preferred_currency ?? 'USD') === 'IDR' && $user->exchange_rate) {
                $limitInIDR = $dailyLimit->max_loss * $user->exchange_rate;
                $projectedInIDR = $newLossUSD * $user->exchange_rate;

                if ($projectedInIDR >= $limitInIDR) {
                    // Block creation: user's view exceeds their selected limit
                    return back()->withErrors([
                        'limit' => 'Perkiraan kerugian melebihi batas loss harian (IDR). Trade dibatalkan.'
                    ])->withInput();
                }
            }

            $isLocked = $newLossUSD >= $dailyLimit->max_loss;
            $dailyLimit->update([
                'current_loss' => $newLossUSD,
                'is_locked'    => $isLocked,
            ]);
        }

        return redirect()->route('trades.index')
            ->with('success', 'Trade berhasil ditambahkan!');
    }

    public function show(Trade $trade)
    {
        abort_if($trade->user_id !== Auth::id(), 403);
        return view('trades.show', compact('trade'));
    }

    public function edit(Trade $trade)
    {
        abort_if($trade->user_id !== Auth::id(), 403);
        return view('trades.form', compact('trade'));
    }

    public function update(Request $request, Trade $trade)
    {
        abort_if($trade->user_id !== Auth::id(), 403);

        $request->validate([
            'instrument'  => 'required|string',
            'type'        => 'required|in:buy,sell',
            'market'      => 'required|in:crypto,forex,saham',
            'entry_price' => 'required|numeric',
            'lot_size'    => 'required|numeric',
            'opened_at'   => 'required|date',
            'status'      => 'required|in:open,closed,cancelled',
        ]);

        $data = $request->all();

        if ($request->stop_loss && $request->take_profit) {
            $risk   = abs($request->entry_price - $request->stop_loss);
            $reward = abs($request->take_profit - $request->entry_price);
            $data['risk_reward'] = $risk > 0 ? round($reward / $risk, 2) : null;
        }

        // Jika status closed atau ada exit_price, hitung ulang PNL
        if (($request->status === 'closed' || $request->filled('exit_price')) && $request->exit_price) {
            $priceDiff = $request->exit_price - $request->entry_price;
            if ($request->type === 'sell') {
                $priceDiff = $request->entry_price - $request->exit_price;
            }

            $instrument = strtoupper($request->instrument ?? '');
            $market = $request->market;
            $contractSize = 1;
            if (str_contains($instrument, 'XAU') || str_contains($instrument, 'GOLD')) {
                $contractSize = 100;
            } elseif ($market === 'forex') {
                $contractSize = 100000;
            }

            $data['pnl'] = round($priceDiff * ($request->lot_size ?? 0) * $contractSize, 2);
            if (!$request->closed_at && $request->status === 'closed') {
                $data['closed_at'] = now();
            }
        }

        $trade->update($data);

        return redirect()->route('trades.index')
            ->with('success', 'Trade berhasil diupdate!');
    }

    public function destroy(Trade $trade)
    {
        abort_if($trade->user_id !== Auth::id(), 403);
        $trade->delete();
        return redirect()->route('trades.index')
            ->with('success', 'Trade berhasil dihapus!');
    }
}
