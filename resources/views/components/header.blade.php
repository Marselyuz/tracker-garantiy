<header class="topbar">
    <a href="{{ url('/') }}" class="brand">
        <img src="{{ asset('img/logo.png') }}" alt="Мои гарантии" class="site-logo">
    </a>

    <button class="burger-btn" id="burgerBtn">☰</button>

    <nav class="nav" id="mobileNav">
        @auth
            @if(Auth::user()->role === 'admin')
                <a href="{{ url('/admin/dashboard') }}" class="nav-link" style="color: #60a5fa; font-weight: bold;">Админ-панель</a>
            @endif
            
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">Сводка</a>
            <a href="{{ route('receipts.index') }}" class="nav-link {{ request()->is('receipts*') ? 'active' : '' }}">Мои чеки</a>
            <a href="{{ route('warranties.index') }}" class="nav-link {{ request()->is('warranties*') ? 'active' : '' }}">Мои гарантии</a>
            <a href="{{ url('/profile') }}" class="nav-link {{ request()->is('profile') ? 'active' : '' }}">Профиль</a>
            
            <form action="{{ url('/logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="nav-link-btn">Выйти</button>
            </form>
        @else
            <a href="{{ url('/login') }}" class="nav-link">Вход</a>
            <a href="{{ url('/register') }}" class="btn">Регистрация</a>
        @endauth
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const burgerBtn = document.getElementById('burgerBtn');
        const mobileNav = document.getElementById('mobileNav');

        if (burgerBtn && mobileNav) {
            burgerBtn.addEventListener('click', function() {
                mobileNav.classList.toggle('active');
                
                if (mobileNav.classList.contains('active')) {
                    burgerBtn.textContent = '✕';
                } else {
                    burgerBtn.textContent = '☰';
                }
            });
        }
    });
</script>