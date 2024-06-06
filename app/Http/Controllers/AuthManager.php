<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthManager extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect(route('home'));
        }
        return view('login');
    }

    public function registration()
    {
        if (Auth::check()) {
            return redirect(route('home'));
        }
        return view('registration');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('home');
        }

        return redirect(route('login'))->with('error', 'Invalid login credentials');
    }

    public function registrationPost(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (!$user) {
            return redirect(route('registration'))->with('error', 'Registration failed');
        }

        return redirect(route('login'))->with('success', 'Registration successful');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect(route('login'));
    }
}