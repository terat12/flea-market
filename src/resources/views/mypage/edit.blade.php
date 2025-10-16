@extends('layouts.app')
@section('title','プロフィール編集')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}"> @endpush

@section('content')
<div class="mypage-edit">
    <h1 class="page-title">住所の変更</h1>

    <form class="form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- 画像 --}}
        <label class="form-row">
            <span class="form-label">プロフィール画像</span>
            <input type="file" name="avatar" accept="image/*">
            @error('avatar') <div class="form-error">{{ $message }}</div> @enderror
        </label>

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
            <a class="btn" href="{{ route('profile.show') }}">キャンセル</a>
        </div>
        
        <input type="hidden" name="return_to" value="{{ old('return_to', request('return_to')) }}">

    </form>
</div>
@endsection