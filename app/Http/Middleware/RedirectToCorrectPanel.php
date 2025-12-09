<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToCorrectPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $panel = Filament::getCurrentPanel();
        
        if (!$panel) {
            return $next($request);
        }

        // Jika user adalah siswa tapi mengakses panel admin
        if ($panel->getId() === 'admin' && ($user->hasRole('student') || $user->hasRole('murid'))) {
            return redirect()->to('/student');
        }

        // Jika user adalah admin/guru tapi mengakses panel student
        if ($panel->getId() === 'student' && $user->hasAnyRole(['admin', 'guru'])) {
            return redirect()->to('/admin');
        }

        return $next($request);
    }
}
