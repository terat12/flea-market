@extends('layouts.app')
@section('title','会員登録')

@section('content')
<div class="container">
    <h1 class="page-title">会員登録</h1>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="form-field">
            <label for="name">ユーザー名</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-field">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-field">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-field">
            <label for="password_confirmation">確認用パスワード</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">登録する</button>
        </div>
    </form>

    <p class="form-subtext">
        すでに登録済みの方は <a class="link" href="{{ route('login') }}">ログインはこちら</a>
    </p>
</div>
@endsection