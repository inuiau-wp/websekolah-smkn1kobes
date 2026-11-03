<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Hanya user dengan role admin yang boleh lanjut.
     * Selain itu 403 (atau redirect ke login jika belum masuk).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            // 404 lebih aman daripada 403 (tidak bocorkan keberadaan area admin)
            abort(404);
        }

        return $next($request);
    }
}
