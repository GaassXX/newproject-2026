<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TradingSummaryController extends Controller
{
    // Exchange rate: Set this based on current market rate
    const USD_TO_IDR_RATE = 15500; // Approximately 15,500 IDR per 1 USD

    /**
     * Display trading summary/recap
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Date range filter
        $period = $request->get('period', '30'); // Default 30 days
        $startDate = match($period) {
            '7' => Carbon::now()->subDays(7),
            '30' => Carbon::now()->subDays(30),
            '90' => Carbon::now()->subDays(90),
            'all' => Carbon::minValue(),
            default => Carbon::now()->subDays(30)
        };

        // Get all trades within period
        $allTrades = Trade::where('user_id', $userId)
            ->where('opened_at', '>=', $startDate)
            ->orderBy('opened_at', 'desc')
            ->get();

        $closedTrades = $allTrades->where('status', 'closed');
        $openTrades = $allTrades->where('status', 'open');

        // Calculate statistics
        $stats = [
            'total_trades' => $closedTrades->count(),
            'total_profit_usd' => $closedTrades->sum('pnl'),
            'total_profit_idr' => $closedTrades->sum('pnl') * self::USD_TO_IDR_RATE,
            'winning_trades' => $closedTrades->filter(fn($t) => ($t->pnl ?? 0) > 0)->count(),
            'losing_trades' => $closedTrades->filter(fn($t) => ($t->pnl ?? 0) < 0)->count(),
            'breakeven_trades' => $closedTrades->filter(fn($t) => ($t->pnl ?? 0) == 0)->count(),
        ];

        // Win rate
        $stats['win_rate'] = $stats['total_trades'] > 0
            ? round(($stats['winning_trades'] / $stats['total_trades']) * 100, 2)
            : 0;

        // Average win and loss
        $winningTrades = $closedTrades->filter(fn($t) => ($t->pnl ?? 0) > 0);
        $losingTrades = $closedTrades->filter(fn($t) => ($t->pnl ?? 0) < 0);

        $stats['avg_win'] = $winningTrades->count() > 0
            ? round($winningTrades->avg('pnl'), 2)
            : 0;
        $stats['avg_loss'] = $losingTrades->count() > 0
            ? round($losingTrades->avg('pnl'), 2)
            : 0;

        // Risk:Reward ratio (safe division)
        $stats['risk_reward'] = 0;
        if ($stats['avg_loss'] != 0 && $stats['avg_loss'] < 0) {
            $stats['risk_reward'] = round(abs($stats['avg_win'] / $stats['avg_loss']), 2);
        } elseif ($stats['avg_win'] > 0 && $stats['avg_loss'] == 0) {
            $stats['risk_reward'] = 'Infinite';
        }

        // Profit factor
        $totalWins = $winningTrades->sum('pnl');
        $totalLosses = abs($losingTrades->sum('pnl'));
        $stats['profit_factor'] = $totalLosses > 0
            ? round($totalWins / $totalLosses, 2)
            : ($totalWins > 0 ? 'Infinite' : 0);

        // Largest win/loss
        $stats['largest_win'] = $closedTrades->max('pnl') ?? 0;
        $stats['largest_loss'] = $closedTrades->min('pnl') ?? 0;

        // By Market breakdown
        $byMarket = [];
        foreach (['forex', 'crypto', 'saham'] as $market) {
            $trades = $closedTrades->where('market', $market);
            if ($trades->count() > 0) {
                $byMarket[$market] = [
                    'count' => $trades->count(),
                    'profit_usd' => $trades->sum('pnl'),
                    'profit_idr' => $trades->sum('pnl') * self::USD_TO_IDR_RATE,
                    'win_rate' => round(($trades->filter(fn($t) => ($t->pnl ?? 0) > 0)->count() / $trades->count()) * 100, 2),
                ];
            }
        }

        // Daily performance (last 30 days)
        $dailyPerformance = $closedTrades
            ->where('closed_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(function($trade) {
                return Carbon::parse($trade->closed_at)->format('Y-m-d');
            })
            ->map(function($trades) {
                return $trades->sum('pnl');
            })
            ->sortKeys();

        return view('trading-summary.index', compact(
            'stats', 'byMarket', 'closedTrades', 'openTrades',
            'dailyPerformance', 'period', 'allTrades'
        ));
    }

    /**
     * Get exchange rate (for updating/getting current rate)
     */
    public function getExchangeRate()
    {
        return response()->json([
            'usd_to_idr' => self::USD_TO_IDR_RATE
        ]);
    }

    /**
     * Calculate pip-based profit for forex/metals
     */
    public static function calculatePipProfit($entryPrice, $exitPrice, $lotSize, $instrument)
    {
        // Determine pip value based on instrument
        $pipValue = match($instrument) {
            'XAU/USD', 'XAG/USD' => 0.01, // 1 pip = $0.01 (for metals)
            default => 0.0001 // 1 pip = 0.0001 for standard forex pairs
        };

        $pips = ($exitPrice - $entryPrice) / $pipValue;

        // Standard lot = 100,000 units for forex, 100 oz for metals
        $standardLot = match($instrument) {
            'XAU/USD', 'XAG/USD' => 100, // troy ounces
            default => 100000 // units
        };

        $profitPerPip = $standardLot / 100000; // Normalize
        if (str_contains($instrument, '/USD')) {
            // For forex pairs, each pip is usually worth $10 per standard lot
            $profitPerPip = 10;
        } elseif ($instrument === 'XAU/USD' || $instrument === 'XAG/USD') {
            // For metals, 1 pip = $1 per ounce
            $profitPerPip = 100;
        }

        $profit = $pips * $profitPerPip * $lotSize;

        return [
            'pips' => round($pips, 2),
            'profit' => round($profit, 2)
        ];
    }
}
