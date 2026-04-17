<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        $user = auth()->user();

        return view('admin.settings.index', compact('user'));
    }

    /**
     * Update user settings.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update trading preferences (preferred currency and rate).
     */
    public function updateTrading(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'preferred_currency' => 'required|in:USD,IDR',
            'exchange_rate' => 'nullable|numeric|min:0',
            'exchange_rate_auto' => 'sometimes|boolean',
        ]);

        $user->preferred_currency = $validated['preferred_currency'];
        $user->exchange_rate = $validated['exchange_rate'] ?? $user->exchange_rate;
        $user->exchange_rate_auto = isset($validated['exchange_rate_auto']) ? (bool)$validated['exchange_rate_auto'] : $user->exchange_rate_auto;
        $user->save();

        return back()->with('success', 'Trading preferences updated.');
    }

    /**
     * Fetch exchange rate (USD -> IDR) from a public API and return JSON.
     */
    public function fetchExchangeRate(Request $request)
    {
        // Simple public API: exchangerate.host
        try {
            $resp = Http::get('https://api.exchangerate.host/latest', [
                'base' => 'USD',
                'symbols' => 'IDR',
            ])->throw();

            $rate = data_get($resp->json(), 'rates.IDR');
            if (! $rate) {
                return response()->json(['ok' => false, 'message' => 'Rate not available'], 422);
            }

            // Optionally save to user if requested
            if ($request->boolean('save')) {
                $user = auth()->user();
                $user->exchange_rate = $rate;
                $user->exchange_rate_auto = true;
                $user->save();
            }

            return response()->json(['ok' => true, 'rate' => (float)$rate]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => 'Unable to fetch rate'], 500);
        }
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Get application statistics (for admin dashboard).
     */
    public function stats()
    {
        return view('admin.settings.stats');
    }
}
