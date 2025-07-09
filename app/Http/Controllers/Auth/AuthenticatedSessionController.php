<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->authenticated($request, Auth::user());
    }

    /**
     * Method yang dipanggil setelah user berhasil login.
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        foreach ($user->roles as $role) {
            if ($role->status === 'disable') {
                Auth::logout();
                return redirect('/login')->withErrors('Your role is not active. Please contact an admin.');
            }
        }

        if ($user->hasPermissionTo('dashboard.superadmin')) {
            return redirect()->route('dashboard.index');
        } elseif ($user->hasPermissionTo('dashboard.employee')) {
            return redirect()->route('dashboardemployee.index');
        }

        return redirect()->route('dashboard.index');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
