<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSiswa
{
    /**
     * Hanya user dengan role siswa yang boleh lanjut.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'siswa') {
            abort(404);
        }

        return $next($request);
    }
}
