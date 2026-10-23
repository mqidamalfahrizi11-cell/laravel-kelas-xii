<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang di Dashboard Admin!');
            }

            return redirect()->intended(route('home'))->with('success', 'Berhasil login!');
        }

        return back()
            ->withInput($request->only('email', 'remember') + ['form_type' => 'login'])
            ->withErrors(['email' => 'Email atau password salah'], 'login');
    }

    public function register(RegisterRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $profile = Profile::create([
                'umur' => 0,
                'bio' => '-',
                'alamat' => '-',
            ]);

            return User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
                'profile_id' => $profile->id,
                'role_id' => 1,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Registrasi berhasil dan Anda telah login!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Berhasil logout!');
    }
}
