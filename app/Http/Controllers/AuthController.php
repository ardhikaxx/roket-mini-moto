<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() || Auth::user()->isKepalaToko() ? 'admin.dashboard' : 'karyawan.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'pin' => 'required|string|size:4'
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->pin // Laravel uses 'password' key to check getAuthPassword()
        ];

        if (Auth::attempt($credentials)) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->with('error', 'Akun Anda telah dinonaktifkan.');
            }
            
            $request->session()->regenerate();
            
            if (Auth::user()->isAdmin() || Auth::user()->isKepalaToko()) {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('karyawan.dashboard'));
        }

        return back()->with('error', 'Username atau PIN salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
