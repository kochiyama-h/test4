@extends('layouts.app')

@section('title', 'ログイン - COACHTECH')

@section('additional_css')
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="login-wrapper">
        <h2 class="page-title">ログイン</h2>

        <form class="login-form" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-button">
                <button type="submit">ログインする</button>
            </div>

            <div class="register-link">
                <a href="{{ route('register') }}">会員登録はこちら</a>
            </div>
        </form>
    </div>+
</div>
@endsection