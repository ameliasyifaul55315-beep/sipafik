<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $key = Str::lower($request->input('email')).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan login. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang kamu masukkan tidak sesuai.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $user = Auth::user();

        // Akun kampus baru dapat digunakan setelah kampus disetujui Super Admin.
        if ($user->role === 'admin_kampus') {
            $campus = $user->campus()->first();
            if (! $campus || $campus->status !== 'approved' || $user->is_active === false) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun kampus belum aktif. Silakan tunggu verifikasi Super Admin.',
                ])->onlyInput('email');
            }
        }

        if (isset($user->is_active) && $user->is_active === false && $user->role !== 'admin_kampus') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'Akun sedang tidak aktif. Hubungi Admin Kampus.'])
                ->onlyInput('email');
        }

        return $this->redirectByRole($user->role);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Kamu berhasil keluar dari SIPAFIK.');
    }

    private function redirectByRole(?string $role)
    {
        if ($role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
