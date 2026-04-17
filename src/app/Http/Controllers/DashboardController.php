<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\DailyLimit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var int|null $userId */
        $userId = Auth::id();

        $trades      = Trade::where('user_id', $userId)->where('status', 'closed')->get();
        $totalTrades = $trades->count();
        $winTrades   = $trades->where('pnl', '>', 0)->count();
        $lossTrades  = $trades->where('pnl', '<', 0)->count();
        $winRate     = $totalTrades > 0 ? round(($winTrades / $totalTrades) * 100, 1) : 0;
        $totalPnl    = $trades->sum('pnl');
        $avgRR       = round($trades->avg('risk_reward') ?? 0, 2);
        $openTrades  = Trade::where('user_id', $userId)->where('status', 'open')->count();

        // Data grafik 30 hari
        $chartTrades = Trade::where('user_id', $userId)
            ->where('status', 'closed')
            ->whereNotNull('pnl')
            ->where('closed_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('closed_at')
            ->get();

        $chartLabels = [];
        $chartData   = [];
        $cumulative  = 0;
        foreach ($chartTrades as $trade) {
            $chartLabels[] = Carbon::parse($trade->closed_at)->format('d M');
            $cumulative   += $trade->pnl;
            $chartData[]   = round($cumulative, 2);
        }

        // Daily limit hari ini
        $dailyLimit = DailyLimit::where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->first();

        $recentTrades = Trade::where('user_id', $userId)
            ->orderBy('opened_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalTrades', 'winTrades', 'lossTrades',
            'winRate', 'totalPnl', 'avgRR', 'openTrades',
            'chartLabels', 'chartData', 'dailyLimit', 'recentTrades'
        ));
    }
}
