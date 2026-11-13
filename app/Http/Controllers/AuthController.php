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

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'cashier' || $user->role === 'admin') {
                return redirect()->intended(route('cashier.dashboard'))->with('success', 'Selamat datang, ' . $user->name . '!');
            }

            return redirect()->intended(route('packages.index'))->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak sesuai.',
        ])->onlyInput('email');
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
            Auth::login($user);
            request()->session()->regenerate();

            if ($user->isCashier()) {
                return redirect()->route('cashier.dashboard')->with('success', 'Beralih ke sesi: ' . $user->name . ' (' . ucfirst($user->role) . ')');
            }

            return redirect()->route('packages.index')->with('success', 'Beralih ke sesi: ' . $user->name . ' (' . ucfirst($user->role) . ')');
        }

        return redirect()->route('login')->with('error', 'Pengguna demo tidak ditemukan.');
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
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('packages.index')->with('success', 'Registrasi berhasil! Selamat datang di KOTHaK Photo.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('packages.index')->with('success', 'Anda telah berhasil keluar (logout).');
    }
}

