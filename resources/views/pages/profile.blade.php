@extends('template.app')
@section('title', 'Профиль')
@section('page')

<div class="profile-layout">
    <section class="profile-card card">
        <h2 class="form-title">Профиль</h2>
        
        <div class="profile-info">
            <div class="info-row">
                <span class="info-label">Имя:</span>
                <span class="info-value">{{ auth()->user()->username }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ auth()->user()->email }}</span>
            </div>
        </div>
    </section>

    <section class="form-card card">
        <h2 class="form-title">Настройки уведомлений</h2>

        <form method="POST" action="{{ route('profile.notifications') }}" class="custom-form">
            @csrf
            @method('PUT')

            <label class="checkbox-row">
                <input type="checkbox" name="notifications_enabled" class="custom-checkbox" value="1" @checked(auth()->user()->notifications_enabled)>
                <span class="checkbox-text">Включить email-уведомления</span>
            </label>

            <div class="form-group" style="margin-top: 10px;">
                <label class="form-label">За сколько дней предупреждать?</label>
                <select name="notify_days" class="form-input" style="background-color: #222; color: #fff;">
                    <option value="1" style="background-color: #222; color: #fff;" @selected(auth()->user()->notify_days == 1)>За 1 день</option>
                    <option value="3" style="background-color: #222; color: #fff;" @selected(auth()->user()->notify_days == 3)>За 3 дня</option>
                    <option value="7" style="background-color: #222; color: #fff;" @selected(auth()->user()->notify_days == 7)>За 7 дней</option>
                    <option value="14" style="background-color: #222; color: #fff;" @selected(auth()->user()->notify_days == 14)>За 14 дней</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Сохранить настройки</button>
            </div>
        </form>
    </section>
</div>

@endsection