@extends('template.app')
@section('title', 'Вход')
@section('page')

<section class="form-card card">
    <h2 class="form-title">Вход</h2>
    
    <form method="POST" action="{{ route('login.post') }}" class="custom-form">
        @csrf

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="text" name="email" class="form-input" placeholder="you@example.com" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Пароль</label>
            <input type="password" name="password" class="form-input" placeholder="••••••••">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-block">Войти</button>
        </div>
    </form>
    
    <p class="form-footer">Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a></p>
</section>

@endsection