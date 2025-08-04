<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\HttpFoundation\Response;

class CheckRoleStatus
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Jika semua role pengguna tidak aktif, logout
        if ($user->roles->every(fn($role) => !$role->isActive())) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['msg' => 'Your role is not active.']);
        }

        return $next($request);
    }
}
