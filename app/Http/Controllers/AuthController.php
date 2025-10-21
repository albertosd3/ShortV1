<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required|string']);
        $pass = env('APP_LOGIN_PASSWORD', 'G666');
        if (hash_equals($pass, (string) $request->input('password'))) {
            $request->session()->put('authed', true);
            return redirect()->route('dashboard');
        }
        return back()->withErrors(['password' => 'Password salah']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('authed');
        return redirect()->route('login');
    }
}
