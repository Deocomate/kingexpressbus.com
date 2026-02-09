<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }
}
