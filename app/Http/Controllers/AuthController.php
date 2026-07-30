<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\LoginHistory;
use App\Services\AuditService;

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

        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();

        // Rate Limiter: Maksimal 5x gagal dalam 5 menit (300 detik)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            return back()->with('error', "Terlalu banyak percobaan PIN salah. Akun/IP Anda terkunci sementara selama {$seconds} detik ({$minutes} menit). Silakan coba lagi nanti.");
        }

        $credentials = [
            'username' => $request->username,
            'password' => $request->pin
        ];

        if (Auth::attempt($credentials)) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->with('error', 'Akun Anda telah dinonaktifkan.');
            }

            // Bersihkan kunci percobaan gagal jika login berhasil
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();
            Auth::user()->update(['last_login_at' => now()]);
            LoginHistory::create([
                'user_id' => Auth::id(),
                'action' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            AuditService::log('login', 'Pengguna ' . Auth::user()->name . ' login ke sistem.');

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif (Auth::user()->isKepalaToko()) {
                return redirect()->intended(route('kepalatoko.dashboard'));
            }
            return redirect()->intended(route('karyawan.dashboard'));
        }

        // Catat percobaan login gagal
        RateLimiter::hit($throttleKey, 300);
        $remaining = RateLimiter::remaining($throttleKey, 5);

        return back()->with('error', "Username atau PIN salah. (Sisa percobaan: {$remaining} kali)");
    }

    public function logout(Request $request)
    {
        LoginHistory::create([
            'user_id' => Auth::id(),
            'action' => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        AuditService::log('logout', 'Pengguna ' . Auth::user()->name . ' logout dari sistem.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function profile()
    {
        return view('profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|alpha_dash|unique:users,username,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ], [
            'username.unique' => 'Username tersebut sudah digunakan oleh pengguna lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
        ]);

        $data = $request->only(['name', 'username', 'phone', 'address']);
        if ($request->hasFile('photo')) {
            if ($user->photo) \App\Helpers\FileUploadHelper::delete($user->photo);
            $data['photo'] = \App\Helpers\FileUploadHelper::upload($request->file('photo'), 'users');
        }
        $user->update($data);
        AuditService::log('update_profile', 'Pengguna ' . $user->name . ' memperbarui profil.');
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePin(Request $request)
    {
        $request->validate([
            'current_pin' => 'required|digits:4',
            'new_pin' => 'required|digits:4|different:current_pin',
            'new_pin_confirmation' => 'required|same:new_pin',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_pin, $user->pin)) {
            return back()->with('error', 'PIN saat ini salah.');
        }

        $user->update(['pin' => Hash::make($request->new_pin)]);
        AuditService::log('change_pin', 'Pengguna ' . $user->name . ' mengganti PIN.');
        return back()->with('success', 'PIN berhasil diganti.');
    }
}
