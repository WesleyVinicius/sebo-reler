<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => 'required|string',
            'senha' => 'required|string',
        ]);

        $sucesso = Auth::guard('funcionario')->attempt([
            'usuario' => $credentials['usuario'],
            'password' => $credentials['senha'],
        ]);

        if ($sucesso) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'usuario' => 'Usuario ou senha incorretos.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('funcionario')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
