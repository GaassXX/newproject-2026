<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DailyLimit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DailyLimitSeeder extends Seeder
{
    /**
     * Seed daily loss limits for test users.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@admin.com')->first();
        $traderUser = User::where('email', 'trader@example.com')->first();

        if (!$adminUser) {
            return;
        }

        // Create daily limits for past 30 days for admin user
        $baseDateLoss = 0;
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $maxLoss = 100; // $100 USD per day (normalized)

            // Simulate realistic loss progression
            if ($i % 5 === 0) {
                $currentLoss = rand(20, 50);
            } elseif ($i % 3 === 0) {
                $currentLoss = rand(50, 80);
            } else {
                $currentLoss = rand(5, 30);
            }

            // Lock only if current loss >= max loss
            $isLocked = $currentLoss >= $maxLoss;

            DailyLimit::firstOrCreate(
                [
                    'user_id' => $adminUser->id,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'max_loss' => $maxLoss,
                    'current_loss' => $isLocked ? $maxLoss : $currentLoss,
                    'is_locked' => $isLocked,
                    'currency' => 'USD', // Always stored as USD
                ]
            );
        }

        // Create daily limit for today
        DailyLimit::firstOrCreate(
            [
                'user_id' => $adminUser->id,
                'date' => Carbon::today(),
            ],
            [
                'max_loss' => 100,
                'current_loss' => 25.50, // Some loss already today
                'is_locked' => false,
                'currency' => 'USD',
            ]
        );

        // If trader user exists, add daily limits too
        if ($traderUser) {
            DailyLimit::firstOrCreate(
                [
                    'user_id' => $traderUser->id,
                    'date' => Carbon::today(),
                ],
                [
                    'max_loss' => 150, // Higher limit for trader
                    'current_loss' => 0,
                    'is_locked' => false,
                    'currency' => 'USD',
                ]
            );
        }
    }
}
