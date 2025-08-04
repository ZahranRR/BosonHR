<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Periksa apakah user yang sedang login memiliki permission yang dibutuhkan
        if (!auth()->user()->can($permission)) {
            // Redirect kembali ke halaman sebelumnya dengan pesan error
            return redirect()->back()->with('error', 'You do not have access to perform this action.');
        }

        return $next($request);
    }
}
