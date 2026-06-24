<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Logic điều hướng
        $redirectTo = $this->resolveRedirect($request, route('client.profile.index'));

        return view('client.auth.login', [
            'redirectTo' => $redirectTo,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'redirect_to' => ['nullable', 'string'],
            'remember' => ['nullable'],
        ], [
            'login.required' => __('client.auth.validation.login_required'),
            'password.required' => __('client.auth.validation.password_required'),
            'password.min' => __('client.auth.validation.password_min_login'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('password'));
        }

        $credentials = $request->only('password');
        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials[$loginType] = $request->input('login');

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $target = $this->resolveRedirect($request, route('client.profile.index'));
            return redirect($target)->with('success', __('client.auth.flash.login_success'));
        }

        return back()->withErrors([
            'login' => __('client.auth.flash.login_invalid'),
        ])->withInput($request->except('password'));
    }

    public function showRegistrationForm(Request $request)
    {
        $redirectTo = $this->resolveRedirect($request, route('client.profile.index'));

        return view('client.auth.register', [
            'redirectTo' => $redirectTo,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'redirect_to' => ['nullable', 'string'],
        ], [
            'name.required' => __('client.auth.validation.name_required'),
            'email.required' => __('client.auth.validation.email_required'),
            'email.email' => __('client.auth.validation.email_invalid'),
            'email.unique' => __('client.auth.validation.email_unique'),
            'phone.unique' => __('client.auth.validation.phone_unique'),
            'password.required' => __('client.auth.validation.password_required'),
            'password.min' => __('client.auth.validation.password_min_register'),
            'password.confirmed' => __('client.auth.validation.password_confirmed'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('password', 'password_confirmation'));
        }

        // Sử dụng Query Builder (DB::table) thay vì Eloquent Create
        $userId = DB::table('users')->insertGetId([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'role' => 'customer', // Mặc định role customer
            'email_verified_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Đăng nhập ngay sau khi đăng ký bằng ID
        Auth::loginUsingId($userId);

        $request->session()->regenerate();

        $target = $this->resolveRedirect($request, route('client.profile.index'));

        return redirect($target)->with('success', __('client.auth.flash.register_success'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.home')->with('success', __('client.auth.flash.logout_success'));
    }

    public function showForgotPasswordForm()
    {
        return view('client.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ], [
            'email.required' => __('client.auth.validation.email_required'),
            'email.email' => __('client.auth.validation.email_invalid'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            Log::info('Password reset link was not sent.', [
                'email' => $request->input('email'),
                'status' => $status,
            ]);
        }

        return back()->with('status', __('client.auth.password.reset_link_sent'));
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('client.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => __('client.auth.validation.email_required'),
            'email.email' => __('client.auth.validation.email_invalid'),
            'password.required' => __('client.auth.validation.password_required'),
            'password.min' => __('client.auth.validation.password_min_register'),
            'password.confirmed' => __('client.auth.validation.password_confirmed'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->input('password')),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('client.login')
                ->with('success', __('client.auth.password.reset_success'));
        }

        return back()->withErrors([
            'email' => __('client.auth.password.reset_invalid_link'),
        ]);
    }

    private function resolveRedirect(Request $request, string $fallback): string
    {
        $target = $request->input('redirect_to');

        if (!is_string($target) || $target === '') {
            return $fallback;
        }

        $target = trim($target);

        if (str_starts_with($target, '/') && !str_starts_with($target, '//')) {
            return url($target);
        }

        $parsedTarget = parse_url($target);
        $parsedAppUrl = parse_url(url('/'));

        if (
            $parsedTarget !== false
            && $parsedAppUrl !== false
            && isset($parsedTarget['host'], $parsedAppUrl['host'])
            && strcasecmp($parsedTarget['host'], $parsedAppUrl['host']) === 0
            && (!isset($parsedTarget['scheme']) || !isset($parsedAppUrl['scheme']) || strcasecmp($parsedTarget['scheme'], $parsedAppUrl['scheme']) === 0)
            && (!isset($parsedTarget['port']) || !isset($parsedAppUrl['port']) || (int) $parsedTarget['port'] === (int) $parsedAppUrl['port'])
        ) {
            $path = $parsedTarget['path'] ?? '/';
            if (!str_starts_with($path, '/')) {
                $path = '/' . $path;
            }
            if (str_starts_with($path, '//')) {
                return $fallback;
            }

            $query = isset($parsedTarget['query']) ? ('?' . $parsedTarget['query']) : '';
            $fragment = isset($parsedTarget['fragment']) ? ('#' . $parsedTarget['fragment']) : '';

            return url($path . $query . $fragment);
        }

        return $fallback;
    }
}
