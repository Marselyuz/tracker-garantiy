<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'banned') {
            // Выходим из аккаунта
            Auth::logout();
            
            // Уничтожаем сессию для безопасности
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Перенаправляем на страницу входа с ошибкой
            return redirect()->route('login')->withErrors([
                'email' => 'Ваш аккаунт был заблокирован администратором.'
            ]);
        }

        return $next($request);
    }
}