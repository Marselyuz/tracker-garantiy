@extends('template.app')
@section('title', 'Регистрация')
@section('page')

<section class="form-card card">
    <h2 class="form-title">Регистрация</h2>
    
    <form method="POST" action="{{ route('register.post') }}" class="custom-form">
        @csrf

        <div class="form-group">
            <label class="form-label">Имя пользователя</label>
            <input type="text" name="username" class="form-input" placeholder="ivan_petrov" value="{{ old('username') }}">
            @error('username')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="text" name="email" class="form-input" placeholder="you@example.com" value="{{ old('email') }}">
            @error('email')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Пароль (мин. 6 символов)</label>
            <input type="password" name="password" class="form-input" placeholder="••••••••">
            @error('password')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Повтор пароля</label>
            <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-block">Создать аккаунт</button>
        </div>
    </form>
    
    <p class="form-footer">Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
</section>

@endsection