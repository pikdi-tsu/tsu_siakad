<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class NeoFeederGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek token Neo Feeder session
        if (!Session::has('neofeeder_token')) {
            Session::put('url.intended', $request->url());

            return redirect()->route('neo_feeder.login')
                ->with('warning', 'Sesi Neo Feeder habis atau belum login. Silakan masuk dulu.');
        }

        return $next($request);
    }
}
