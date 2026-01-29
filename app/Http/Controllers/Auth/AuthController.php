<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->getEmail();
            $allowedDomains = explode(',', env('ALLOWED_DOMAINS', 'saludaysen.cl'));
            $domain = substr(strrchr($email, "@"), 1);

            if (!in_array($domain, $allowedDomains)) {
                return redirect('/login')->with('error', 'Dominio no permitido.');
            }

            $user = User::updateOrCreate([
                'email' => $email,
            ], [
                'name' => $googleUser->getName(),
                'password' => bcrypt(Str::random(24)), // Random password for Socialite users
            ]);

            Auth::login($user);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error en la autenticación con Google.');
        }
    }

    public function localLogin(\Illuminate\Http\Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('username');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
