<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized *'], 401);
        }

        // For admin users
        if ($request->user() instanceof \App\Models\User && $request->user()->type === 'admin') {
            return $next($request);
        }

        // For blood banks
        if ($request->user() instanceof \App\Models\BloodBank) {
            if (in_array('blood_bank', $roles)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
} 