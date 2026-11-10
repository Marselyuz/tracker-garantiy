@extends('template.app')
@section('title', 'Трекер гарантий — главная')
@section('page')

<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">🚀 Твой цифровой сейф</p>
        <h1 class="hero-title">
            Чеки и гарантии <span class="text-gradient">в одном месте</span> — без бумажек
        </h1>
        <p class="hero-lead">Загружайте чеки, указывайте срок гарантии на каждый товар и получайте уведомления до того, как срок истечёт. Ваши права потребителя всегда под рукой.</p>
        
        <div class="hero-actions">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Начать бесплатно</a>
            <a href="{{ route('login') }}" class="btn btn-outline btn-lg">Уже есть аккаунт</a>
        </div>
    </div>
</section>

<section class="features">
    <h2 class="features-title">Как это работает?</h2>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon-box">🧾</div>
            <h3>1. Загрузка чека</h3>
            <p>Добавьте фото чека или укажите данные вручную: магазин, дата покупки и список товаров.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon-box">⏱️</div>
            <h3>2. Срок гарантии</h3>
            <p>Укажите срок гарантии (в месяцах) для каждого товара — система сама высчитает дату окончания.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon-box">🔔</div>
            <h3>3. Напоминания</h3>
            <p>Включите уведомления, чтобы узнать об истекающей гарантии заранее и успеть сдать товар в ремонт.</p>
        </div>
    </div>
</section>

@endsection