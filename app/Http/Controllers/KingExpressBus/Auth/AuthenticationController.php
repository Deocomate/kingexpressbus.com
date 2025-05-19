<?php

namespace App\Http\Controllers\KingExpressBus\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login()
    {
        return view('kingexpressbus.auth.login');
    }

    public function logout()
    {
        Auth::logout();
        return to_route('admin.login');
    }


    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->remember != null;

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return to_route("admin.dashboard.index");
        }

        return back()->withErrors([
            'error' => 'Đăng nhập thất bại',
        ])->onlyInput('email');
    }
}
