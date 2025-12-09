<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AutoLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeout = config('session.lifetime') * 60; // Convert minutes to seconds
            $lastActivity = Session::get('last_activity_time');

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                Auth::logout();
                Session::flush();
                
                return redirect()->route('filament.admin.auth.login')
                    ->with('message', 'Sesi Anda telah berakhir karena tidak aktif. Silakan login kembali.');
            }

            Session::put('last_activity_time', time());
        }

        return $next($request);
    }
}
