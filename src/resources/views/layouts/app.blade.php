<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','coachtech フリマ')</title>

    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">

    @stack('styles')
    @yield('css')
</head>

<body>
    <header class="site-header">
        <div class="header-bar container">
            <a class="logo" href="{{ auth()->check() ? route('products.index') : route('products.index.guest') }}">
                <img src="{{ asset('logo.svg') }}" alt="COACHTECH" class="logo-img">
            </a>

            <form class="search" action="{{ auth()->check() ? route('products.index') : route('products.index.guest') }}" method="GET" role="search">
                <input class="search-input" type="search" name="q" placeholder="なにをお探しですか？" value="{{ request('q') }}">
            </form>

            <nav class="nav">
                @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-link" type="submit">ログアウト</button>
                </form>
                <a class="nav-link" href="{{ route('profile.show') }}">マイページ</a>
                <a class="btn btn--primary" href="{{ route('products.create') }}">出品</a>
                @endauth

                @guest
                <a class="nav-link" href="{{ route('login') }}">ログイン</a>
                <a class="btn btn--primary" href="{{ route('register') }}">会員登録</a>
                @endguest
            </nav>
        </div>
        <div class="header-underline"></div>
    </header>

    <main class="container">
        @if (session('status'))
        <div class="flash flash--success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
        <div class="flash flash--error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
        <div class="flash flash--error">
            <ul>
                @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container">
            <small>&copy; {{ date('Y') }} coachtech</small>
        </div>
    </footer>
</body>

</html>