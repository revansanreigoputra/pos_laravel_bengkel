<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // --- PERUBAHAN DIMULAI DI SINI ---
        // Logika pengalihan ini akan dihapus atau diubah
        // agar semua role (termasuk admin dan kasir) diarahkan ke 'dashboard'
        
        // Baris ini akan dihapus atau dikomentari:
        // $user = \Illuminate\Support\Facades\Auth::user();
        // if ($user && $user->hasRole('kasir')) {
        //     return redirect()->route('dashboard.kasir');
        // }

        // Cukup biarkan pengalihan default ke intended atau dashboard utama
        return redirect()->intended(route('dashboard', absolute: false));
        // --- PERUBAHAN BERAKHIR DI SINI ---
    }

    /** 
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}