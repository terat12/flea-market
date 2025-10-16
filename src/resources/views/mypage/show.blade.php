@extends('layouts.app')
@section('title','マイページ')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('content')
<div class="mypage">
    <div class="mypage-head">
        <div class="avatar">
            @if($user->avatar_path ?? false)
            <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="avatar">
            @else
            <div class="avatar--placeholder"></div>
            @endif
        </div>
        <h1 class="username">{{ $user->name ?? 'ユーザー名' }}</h1>
        <a class="btn btn--primary btn-edit" href="{{ route('profile.edit') }}">プロフィールを編集</a>
    </div>

    <div class="tabs">
        <a href="{{ route('profile.show', ['tab' => 'listed']) }}"
            class="tab {{ $tab === 'listed' ? 'tab--active' : '' }}">出品した商品</a>
        <a href="{{ route('profile.show', ['tab' => 'purchased']) }}"
            class="tab {{ $tab === 'purchased' ? 'tab--active' : '' }}">購入した商品</a>
    </div>

    @include('partials.product-grid', ['products' => $items])
</div>
@endsection