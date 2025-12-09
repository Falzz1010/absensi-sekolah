<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockStudentFromAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ONLY block if trying to access admin panel (not student panel)
        // Check if the request is for admin panel
        if ($request->is('admin') || $request->is('admin/*')) {
            // If user is authenticated and has student/murid role, block access
            if (auth()->check() && auth()->user()->hasAnyRole(['student', 'murid'])) {
                // Logout the user
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect to student panel with error message
                return redirect()->route('filament.student.auth.login')
                    ->with('error', 'Anda tidak memiliki akses ke panel admin. Silakan login di panel siswa.');
            }
        }

        return $next($request);
    }
}
