<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function loginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    //Tampilkan halaman register
    public function registerForm()
    {
        return view('auth.register');
    }


    // Proses register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:2|confirmed',
        ]);

        $user_model = new User;
        $user = $user_model->get_user()->where("email", $request['email'])->first();

        if (!empty($user)) {
            return redirect()->route('register')->with(['error' => 'Email yang anda masukkan sudah terdaftar.']);
        }

        User::createUser($request);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat!');
    }

    //Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
        
    }