@extends('layouts.app')
@section('title','メール認証')
@section('content')
<h1>メール認証が必要です</h1>
<p>登録メール宛に認証リンクを送信しました。メール内のリンクをクリックしてください。</p>

@if (session('status') === 'verification-link-sent')
<div class="flash flash--success">認証メールを再送しました。</div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button class="btn btn--primary">認証メールを再送する</button>
</form>
@endsection