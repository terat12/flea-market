@extends('layouts.app')
@section('title','プロフィール編集')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}"> @endpush

@section('content')
<div class="mypage-edit">

    @php
    use Illuminate\Support\Str; // ←追記（FQCNで書くなら不要）

    $needFirstSetup = empty($user->zipcode) || empty($user->address);

    // 元の return_to（購入画面から来たときだけ入っている）
    $rawReturnTo = old('return_to', request('return_to'));
    $isFromPurchase = filled($rawReturnTo);

    // 内部URLだけを許可。外部不正はマイページへフォールバック
    $safeReturnTo = ($isFromPurchase && Str::startsWith($rawReturnTo, url('/')))
    ? $rawReturnTo
    : route('profile.show');
    @endphp


    {{-- 購入 → 住所の変更、それ以外 → プロフィール設定 --}}
    <h1 class="page-title">{{ $isFromPurchase ? '住所の変更' : 'プロフィール設定' }}</h1>

    <form class="form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 丸アイコン＋ボタン --}}
        <div class="avatar-field">
            <div class="avatar avatar-preview">
                @if($user->avatar_path ?? false)
                <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="avatar">
                @else
                <div class="avatar--placeholder"></div>
                @endif
            </div>

            <input id="avatar" type="file" name="avatar" accept="image/*" class="visually-hidden">
            <label for="avatar" class="btn btn--secondary">画像を選択する</label>
        </div>
        @error('avatar') <div class="form-error">{{ $message }}</div> @enderror

        <label class="form-row">
            <span class="form-label">ユーザー名</span>
            <input type="text" name="name" value="{{ old('name',$user->name ?? '') }}">
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </label>

        <label class="form-row">
            <span class="form-label">郵便番号</span>
            <input type="text" name="zipcode" value="{{ old('zipcode',$user->zipcode ?? '') }}" placeholder="123-4567">
            @error('zipcode') <div class="form-error">{{ $message }}</div> @enderror
        </label>

        <label class="form-row">
            <span class="form-label">住所</span>
            <input type="text" name="address" value="{{ old('address',$user->address ?? '') }}">
            @error('address') <div class="form-error">{{ $message }}</div> @enderror
        </label>

        <label class="form-row">
            <span class="form-label">建物名</span>
            <input type="text" name="building" value="{{ old('building',$user->building ?? '') }}">
            @error('building') <div class="form-error">{{ $message }}</div> @enderror
        </label>

        <div class="form-actions">
            <button class="btn btn--primary" type="submit">更新する</button>
            <a class="btn"
                href="{{ $isFromPurchase ? $safeReturnTo : route('profile.show') }}">キャンセル</a>
        </div>

        {{-- 保存後の戻り先コントロール --}}
        <input type="hidden" name="return_to" value="{{ $isFromPurchase ? $safeReturnTo : '' }}">
        @if ($needFirstSetup && !$isFromPurchase)
        {{-- 初回セットアップの目印（保存後にトップへ返す） --}}
        <input type="hidden" name="first_setup" value="1">
        @endif
    </form>
</div>
@endsection