<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next, ...$roles)
{
    $user = Auth::user();

    if (!Auth::check() || !$user->hasAnyRole($roles)) {
        abort(403, 'You do not have access to this area.');
    }

    return $next($request);
}

}
