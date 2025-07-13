<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek user admin saja
        $user = User::where('email', $request->email)->where('peran', 'admin')->first();
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->intended('/dashboard'); // ganti sesuai halaman admin
        }

        return back()->withErrors(['email' => 'Email atau password salah, atau Anda bukan admin.']);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'peran' => 'admin', // hanya admin yang bisa daftar di web
        ]);

        // Setelah registrasi, redirect ke login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
} 