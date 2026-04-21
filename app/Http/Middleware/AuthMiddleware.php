<?php

namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
 
class AuthSession
{
    public function handle(Request $request, Closure $next)
    {
        // Belum login sama sekali
        if (!session('user_id')) {
            return redirect()->route('login');
        }
 
        // Admin tidak butuh kantor_id
        if (session('user_role') === 'admin') {
            return $next($request);
        }
 
        // Role lain wajib punya kantor aktif
        if (!session('kantor_id')) {
            // Mungkin masih di step pilih kantor
            if (session('kantor_pilihan')) {
                return redirect()->route('pilih.kantor');
            }
            return redirect()->route('login');
        }
 
        return $next($request);
    }
}