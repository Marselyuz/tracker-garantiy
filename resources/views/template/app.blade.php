<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#121212">
    <title>@yield('title', 'Трекер гарантий')</title>
    <style>html, body { background-color: #121212; color-scheme: dark; }</style>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="dark-theme">
    <div class="wrap">
        @include('components.header')
        
        @include('components.alert')
        
        <main class="main-content">
            @yield('page')
        </main>
    </div>
</body>
</html>