<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Paksa user mengganti password sementara sebelum memakai aplikasi.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->must_change_password && ! $request->routeIs('password.edit', 'password.update', 'logout')) {
            return redirect()->route('password.edit');
        }

        return $next($request);
    }
}
