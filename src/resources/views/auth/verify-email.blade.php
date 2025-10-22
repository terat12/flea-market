@extends('layouts.app')
@section('title','メール認証')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth auth--narrow center">
    <p class="verify-copy">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    {{-- MailHog など確認ツールへ。環境に合わせて .env の MAILHOG_URL で上書き可 --}}
    <a class="btn btn--ghost" href="{{ env('MAILHOG_URL', 'http://localhost:8025') }}" target="_blank" rel="noopener">
        認証はこちらから
    </a>

    <form method="POST" action="{{ route('verification.send') }}" class="resend">
        @csrf
        <button type="submit" class="link-button">認証メールを再送する</button>
    </form>

    @if (session('status') === 'verification-link-sent')
    <div class="flash flash--success">認証メールを再送しました。</div>
    @endif
</div>
@endsection