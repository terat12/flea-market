@extends('layouts.app')
@section('title','ログイン')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth">
    <h1 class="page-title">ログイン</h1>

    <form class="form" method="POST" action="{{ route('login') }}">
        @csrf

        <label class="form-label">メールアドレス</label>
        <input class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label class="form-label">パスワード</label>
        <input class="form-input" type="password" name="password" required autocomplete="current-password">

        <label class="form-check">
            <input type="checkbox" name="remember"> ログイン状態を保持する
        </label>

        <button class="btn btn--primary" type="submit">ログインする</button>
    </form>

    <p class="form-subtext">
        アカウントをお持ちでない方は
        <a class="link" href="{{ route('register') }}">会員登録はこちら</a>
    </p>
</div>
@endsection