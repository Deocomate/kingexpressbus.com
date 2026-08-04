<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends \App\Http\Controllers\Controller
{
    public function loginForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->credentials();

        // Validate credentials WITHOUT logging in — prevents oracle attack.
        // Auth::attempt() followed by logout() would confirm valid credentials
        // for any account, including customer accounts.
        if (! Auth::validate($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if (! $user || ! $user->isAdmin()) {
            // Same error message regardless of whether user exists or wrong role
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
