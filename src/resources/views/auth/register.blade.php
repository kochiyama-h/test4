@extends('layouts.app')

@section('title', '会員登録 - COACHTECH')

@section('additional_css')
    <link href="{{ asset('css/register.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="register-wrapper">
        <h2 class="page-title">会員登録</h2>

        <form class="register-form" method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="name">名前</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" >
            </div>
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" >
            </div>
            @error('email')
                <span class="error">{{ $message }}</span>
            @enderror

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" >
            </div>
            @error('password')
                <span class="error">{{ $message }}</span>
            @enderror

            <div class="form-group">
                <label for="password_confirmation">パスワード確認</label>
                <input type="password" id="password_confirmation" name="password_confirmation" >
            </div>
            @error('password_confirmation')
                <span class="error">{{ $message }}</span>
            @enderror
            

            <div class="form-button">
                <button type="submit">登録する</button>
            </div>

            <div class="login-link">
                <a href="{{ route('login') }}">ログインはこちら</a>
            </div>
        </form>
    </div>
</div>
@endsection