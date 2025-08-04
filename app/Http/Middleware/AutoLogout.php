<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AutoLogout
{
    public function handle($request, Closure $next)
    {
        $timeout = 3600; // 1 jam (dalam detik)
        if (Auth::check()) {
            $lastActivity = Session::get('last_activity');
            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                Auth::logout();
                Session::flush();
                return redirect()->route('login')->with('message', 'You have logged out due to inactivity.');
            }
            Session::put('last_activity', time());
        }

        return $next($request);
    }
}
