<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AccountController extends Controller
{
    public function showRegisterForm()
    {
        return view('pages.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
        'username' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'password_confirmation' => 'required|same:password',
    ], [
        
        'username.required' => 'Пожалуйста, введите имя пользователя.',
        'username.max' => 'Имя пользователя слишком длинное.',
        
        
        'email.required' => 'Пожалуйста, укажите email.',
        'email.email' => 'Введите корректный email адрес.', 
        'email.unique' => 'Пользователь с таким email уже существует.',
        
        
        'password.required' => 'Пожалуйста, введите пароль.',
        'password.min' => 'Пароль должен содержать минимум 6 символов.', 
        'password_confirmation.required' => 'Пожалуйста, повторите пароль.',
        'password_confirmation.same' => 'Пароли не совпадают.',
    ]);

    $data['password'] = Hash::make($data['password']);
    
    
    $user = User::create($data);
    Auth::login($user);

    return redirect('/dashboard')->with('success', 'Регистрация успешна!');
    }

    public function showLoginForm()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ], [
        'email.required' => 'Введите email.',
        'email.email' => 'Введите корректный email адрес.',
        'password.required' => 'Введите пароль.',
    ]);

        if (Auth::attempt($validated)) {
            return redirect('/dashboard')->with('success', 'Добро пожаловать!');
        }

        return back()->withErrors([
            'email' => 'Неверный email или пароль',
        ])->withInput();
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Вы вышли из аккаунта');
    }

    public function profile()
    {
        return view('pages.profile');
    }

    public function updateNotifications(Request $request)
    {
        $user = auth()->user();
    
        $user->notifications_enabled = $request->has('notifications_enabled');
        
        $user->notify_days = $request->input('notify_days', 7);
        
        $user->save();

        return back()->with('success', 'Настройки уведомлений обновлены');
    }
}
