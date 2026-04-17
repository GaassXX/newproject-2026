<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Signal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SignalController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'instrument' => 'required|string',
            'side' => 'required|in:buy,sell',
            'volume' => 'required|numeric',
            'take_profit' => 'nullable|numeric',
            'stop_loss' => 'nullable|numeric',
        ]);

        $signal = Signal::create($data + ['status' => 'pending']);
        Log::info('Signal created', ['signal_id' => $signal->id, 'instrument' => $signal->instrument, 'side' => $signal->side]);

        return response()->json(['ok' => true, 'signal' => $signal], 201);
    }

    // Endpoint for EA to fetch the next pending signal
    public function next()
    {
        Log::debug('EA polling for next signal');
        $signal = Signal::where('status', 'pending')->orderBy('id')->first();
        if (! $signal) {
            Log::debug('No pending signals available');
            return response()->json(['ok' => false, 'message' => 'no signal'], 204);
        }

        // Mark as sent so EA won't pick it again
        $signal->update(['status' => 'sent']);
        Log::info('Signal sent to EA', ['signal_id' => $signal->id, 'instrument' => $signal->instrument, 'side' => $signal->side, 'volume' => $signal->volume]);

        return response()->json(['ok' => true, 'signal' => $signal]);
    }

    // EA calls this to report execution results
    public function executed(Request $request, Signal $signal)
    {
        // simple token auth: set EA_API_TOKEN in .env
        $token = $request->header('X-EA-Token');
        Log::debug('Received execution callback', ['signal_id' => $signal->id, 'has_token' => !is_null($token)]);

        if (!hash_equals(env('EA_API_TOKEN', ''), (string) $token)) {
            Log::warning('Unauthorized execution callback', ['signal_id' => $signal->id, 'ip' => $request->ip()]);
            return response()->json(['ok' => false, 'message' => 'unauthorized'], 401);
        }

        $data = $request->validate([
            'ticket' => 'nullable|string',
            'status' => 'required|string',
            'executed_price' => 'nullable|numeric',
            'executed_at' => 'nullable|date',
        ]);

        $signal->update([
            'status' => $data['status'],
            'remote_ticket' => $data['ticket'] ?? null,
            'executed_price' => $data['executed_price'] ?? null,
            'executed_at' => $data['executed_at'] ?? now(),
            'executed_by' => 'mt5-ea',
        ]);
        Log::info('Signal execution confirmed', ['signal_id' => $signal->id, 'status' => $data['status'], 'ticket' => $data['ticket'] ?? 'null', 'executed_price' => $data['executed_price'] ?? 'null']);

        return response()->json(['ok' => true, 'signal' => $signal]);
    }
}
