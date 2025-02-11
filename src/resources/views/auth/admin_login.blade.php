@extends('layouts.app')

@section('title', '管理者ログイン')

@section('additional_css')
<link rel="stylesheet" href="{{ asset('css/admin_login.css') }}">
@endsection

@section('content')
<div class="login-container">
    <div class="login-content">
        <h1 class="login-title">管理者ログイン</h1>

        <form method="POST" action="{{ route('admin.login') }}" class="login-form">
            @csrf

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    autofocus
                    class="form-input @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="form-input @error('password') is-invalid @enderror"
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="login-button">
                管理者ログインする
            </button>
        </form>
    </div>
</div>
@endsection