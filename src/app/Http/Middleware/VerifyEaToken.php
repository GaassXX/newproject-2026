<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyEaToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-EA-Token');
        if (! hash_equals((string) env('EA_API_TOKEN', ''), (string) $token)) {
            return response()->json(['ok' => false, 'message' => 'unauthorized'], 401);
        }

        return $next($request);
    }
}
