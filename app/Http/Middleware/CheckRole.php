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
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        
        // Cek apakah user memiliki salah satu role yang diizinkan
        foreach ($roles as $role) {
            if ($role === 'super_admin' && $user->isSuperAdmin()) {
                return $next($request);
            }
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }
            if ($role === 'relawan' && ($user->isRelawan() || $user->isAdmin())) {
                return $next($request);
            }
        }

        // Jika tidak memiliki role yang sesuai
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
