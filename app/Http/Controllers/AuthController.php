<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', strtolower($credentials['email']))->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ])->onlyInput('email');
        }

        // Login is strictly for cashier and admin only
        if ($user->role === 'customer') {
            return back()->withErrors([
                'email' => 'Halaman login ini khusus staf kasir dan admin studio. Pelanggan dapat langsung mengambil antrean tanpa perlu login.',
            ])->onlyInput('email');
        }

        // Check active status
        if (! $user->is_active) {
            return back()->withErrors([
                'email' => 'Akun Anda dinonaktifkan oleh administrator studio. Silakan hubungi kasir atau admin.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang Admin KOTHaK Photo Studio!');
        }

        if ($user->role === 'cashier') {
            return redirect()->intended(route('cashier.dashboard'))->with('success', 'Selamat bertugas, Kasir '.$user->name.'!');
        }

        return redirect()->intended(route('packages.index'));
    }

    public function quickLogin(string $role): RedirectResponse
    {
        $roleMap = [
            'cashier' => 'cashier@kothakphoto.id',
            'admin' => 'admin@kothakphoto.id',
        ];

        if (! isset($roleMap[$role])) {
            return redirect()->route('packages.index')->with('info', 'Pelanggan dapat langsung mengambil antrean tanpa perlu login.');
        }

        $email = $roleMap[$role];
        $user = User::where('email', $email)->first();

        if ($user) {
            if (! $user->is_active) {
                $user->update(['is_active' => true]);
            }

            Auth::login($user);
            request()->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Beralih ke sesi Admin Studio: '.$user->name);
            }

            if ($user->role === 'cashier') {
                return redirect()->route('cashier.dashboard')->with('success', 'Beralih ke sesi Kasir / Operator: '.$user->name);
            }
        }

        return redirect()->route('login')->with('error', 'Akun demo peran '.$role.' tidak ditemukan.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda berhasil keluar dari sistem.');
    }
}
