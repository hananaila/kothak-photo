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

        // FR-01: Check active status
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

        return redirect()->intended(route('packages.index'))->with('success', 'Selamat datang kembali di KOTHaK Photo, '.$user->name.'!');
    }

    public function quickLogin(string $role): RedirectResponse
    {
        $roleMap = [
            'customer' => 'hananaeylaa@gmail.com',
            'cashier' => 'cashier@kothakphoto.id',
            'admin' => 'admin@kothakphoto.id',
        ];

        $email = $roleMap[$role] ?? 'hananaeylaa@gmail.com';
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

            return redirect()->route('packages.index')->with('success', 'Beralih ke sesi Pelanggan: '.$user->name);
        }

        return redirect()->route('login')->with('error', 'Akun demo peran '.$role.' tidak ditemukan.');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'phone_whatsapp' => ['required', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone_whatsapp' => $validated['phone_whatsapp'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('packages.index')->with('success', 'Pendaftaran akun berhasil! Selamat datang di KOTHaK Photo Purbalingga.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('packages.index')->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
