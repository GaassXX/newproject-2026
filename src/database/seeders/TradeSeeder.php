<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Trade;
use App\Models\DailyLimit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@admin.com')->first();

        if (!$adminUser) {
            return;
        }

        // Create sample closed trades with various profit/loss
        $trades = [
            [
                'instrument' => 'EUR/USD',
                'type' => 'buy',
                'market' => 'forex',
                'entry_price' => 1.0800,
                'exit_price' => 1.0850,
                'stop_loss' => 1.0750,
                'take_profit' => 1.0900,
                'lot_size' => 1,
                'pnl' => 500,
                'risk_reward' => 2.5,
                'status' => 'closed',
                'strategy' => 'Support Resistance',
                'notes' => 'Great entry at support level. Hit TP at first resistance.',
                'opened_at' => Carbon::now()->subDays(25),
                'closed_at' => Carbon::now()->subDays(24),
            ],
            [
                'instrument' => 'GBP/USD',
                'type' => 'sell',
                'market' => 'forex',
                'entry_price' => 1.2650,
                'exit_price' => 1.2600,
                'stop_loss' => 1.2700,
                'take_profit' => 1.2500,
                'lot_size' => 1,
                'pnl' => -150,
                'risk_reward' => 1.5,
                'status' => 'closed',
                'strategy' => 'Trend Following',
                'notes' => 'Hit stop loss. Trend was too strong.',
                'opened_at' => Carbon::now()->subDays(23),
                'closed_at' => Carbon::now()->subDays(22),
            ],
            [
                'instrument' => 'BTC/USDT',
                'type' => 'buy',
                'market' => 'crypto',
                'entry_price' => 45000,
                'exit_price' => 46200,
                'stop_loss' => 44500,
                'take_profit' => 47000,
                'lot_size' => 0.05,
                'pnl' => 2100,
                'risk_reward' => 3.2,
                'status' => 'closed',
                'strategy' => 'Technical Breakout',
                'notes' => 'Good entry at key support. Exited at minor resistance.',
                'opened_at' => Carbon::now()->subDays(20),
                'closed_at' => Carbon::now()->subDays(19),
            ],
            [
                'instrument' => 'AAPL',
                'type' => 'buy',
                'market' => 'saham',
                'entry_price' => 150.25,
                'exit_price' => 152.00,
                'stop_loss' => 148.50,
                'take_profit' => 155.00,
                'lot_size' => 10,
                'pnl' => 175,
                'risk_reward' => 2.0,
                'status' => 'closed',
                'strategy' => 'Momentum',
                'notes' => 'Price action confirmation before entry.',
                'opened_at' => Carbon::now()->subDays(18),
                'closed_at' => Carbon::now()->subDays(17),
            ],
            [
                'instrument' => 'ETH/USDT',
                'type' => 'sell',
                'market' => 'crypto',
                'entry_price' => 2500,
                'exit_price' => 2450,
                'stop_loss' => 2550,
                'take_profit' => 2350,
                'lot_size' => 0.2,
                'pnl' => 300,
                'risk_reward' => 2.5,
                'status' => 'closed',
                'strategy' => 'Channel Breakout',
                'notes' => 'Perfect short entry on resistance rejection.',
                'opened_at' => Carbon::now()->subDays(15),
                'closed_at' => Carbon::now()->subDays(14),
            ],
            [
                'instrument' => 'AUD/USD',
                'type' => 'buy',
                'market' => 'forex',
                'entry_price' => 0.6700,
                'exit_price' => 0.6680,
                'stop_loss' => 0.6650,
                'take_profit' => 0.6800,
                'lot_size' => 2,
                'pnl' => -400,
                'risk_reward' => 2.0,
                'status' => 'closed',
                'strategy' => 'Range Trading',
                'notes' => 'Quick exit due to false breakout.',
                'opened_at' => Carbon::now()->subDays(12),
                'closed_at' => Carbon::now()->subDays(11),
            ],
            [
                'instrument' => 'MSFT',
                'type' => 'buy',
                'market' => 'saham',
                'entry_price' => 375.50,
                'exit_price' => 382.00,
                'stop_loss' => 370.00,
                'take_profit' => 390.00,
                'lot_size' => 5,
                'pnl' => 325,
                'risk_reward' => 2.8,
                'status' => 'closed',
                'strategy' => 'Trend Following',
                'notes' => 'Strong uptrend. Clean 5-day trade.',
                'opened_at' => Carbon::now()->subDays(10),
                'closed_at' => Carbon::now()->subDays(5),
            ],
            [
                'instrument' => 'USD/JPY',
                'type' => 'buy',
                'market' => 'forex',
                'entry_price' => 150.50,
                'exit_price' => 151.80,
                'stop_loss' => 149.50,
                'take_profit' => 153.00,
                'lot_size' => 1,
                'pnl' => 650,
                'risk_reward' => 3.25,
                'status' => 'closed',
                'strategy' => 'News Trading',
                'notes' => 'Good setup after economic data release.',
                'opened_at' => Carbon::now()->subDays(8),
                'closed_at' => Carbon::now()->subDays(7),
            ],
        ];

        foreach ($trades as $tradeData) {
            $tradeData['user_id'] = $adminUser->id;
            Trade::create($tradeData);
        }

        // Create open trades
        Trade::create([
            'user_id' => $adminUser->id,
            'instrument' => 'XAU/USD',
            'type' => 'buy',
            'market' => 'forex',
            'entry_price' => 2050.00,
            'exit_price' => null,
            'stop_loss' => 2030.00,
            'take_profit' => 2100.00,
            'lot_size' => 0.5,
            'pnl' => null,
            'risk_reward' => null,
            'status' => 'open',
            'strategy' => 'Consolidation Breakout',
            'notes' => 'Waiting for confirmation to break above 2060 level.',
            'opened_at' => Carbon::now()->subDays(2),
            'closed_at' => null,
        ]);

        Trade::create([
            'user_id' => $adminUser->id,
            'instrument' => 'BTC/USDT',
            'type' => 'sell',
            'market' => 'crypto',
            'entry_price' => 48000,
            'exit_price' => null,
            'stop_loss' => 49500,
            'take_profit' => 46000,
            'lot_size' => 0.1,
            'pnl' => null,
            'risk_reward' => null,
            'status' => 'open',
            'strategy' => 'Resistance Rejection',
            'notes' => 'Bearish divergence at resistance.',
            'opened_at' => Carbon::now()->subDay(),
            'closed_at' => null,
        ]);

        // Create daily limit for today
        DailyLimit::create([
            'user_id' => $adminUser->id,
            'max_loss' => 500,
            'current_loss' => 150,
            'is_locked' => false,
            'date' => Carbon::today(),
        ]);

        // Create daily limits for past days
        for ($i = 1; $i <= 5; $i++) {
            DailyLimit::create([
                'user_id' => $adminUser->id,
                'max_loss' => 500,
                'current_loss' => rand(50, 400),
                'is_locked' => false,
                'date' => Carbon::today()->subDays($i),
            ]);
        }
    }
}
