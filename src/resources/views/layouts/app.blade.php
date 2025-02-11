<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'COACHTECH')</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @yield('additional_css')
</head>
<body>
    <header>
        <div class="header-content">
            <h1 class="logo">
            <a class="header__logo" href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="coachtechロゴ">          
            </a>
            </h1>
            @auth
                <nav class="nav-menu">
                    @if(Auth::user()->is_admin == 1)
                        <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
                        <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
                        <a href="{{ route('admin.request') }}">申請一覧</a>
                    @else
                        <a href="{{ route('attendance.index') }}">勤怠</a>
                        <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                        <a href="{{ route('request') }}">申請一覧</a>                        
                    @endif
                    
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-button">ログアウト</button>
                    </form>
                </nav>
            @endauth
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>