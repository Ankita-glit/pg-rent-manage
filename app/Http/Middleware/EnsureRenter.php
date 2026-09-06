<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRenter
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isRenter()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized renter access.'], 403);
            }
            return redirect()->route('login')->with('error', 'Renter portal access required.');
        }

        return $next($request);
    }
}
