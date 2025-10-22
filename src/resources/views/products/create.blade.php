@extends('layouts.app')
@section('title','商品の出品')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/product-create.css') }}">
@endpush

@section('content')
<h1 class="page-title">商品の出品</h1>

<form class="sell-form" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" novalidate>
    @csrf

    {{-- 画像 --}}
    <div class="form-block">
        <label class="form-label" for="image">商品画像</label>

        {{-- 初期ボタンを隠し、ラベルをドロップ枠に変える --}}
        <label for="image" class="image-drop">
            画像を選択する
            <input id="image" type="file" name="image" accept="image/*" class="visually-hidden">
        </label>

        @error('image')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <h2 class="section-title">商品の詳細</h2>

    {{-- カテゴリ --}}
    @php
    $categories = [
    'ファッション','家電','インテリア','レディース','メンズ','コスメ',
    '本','ゲーム','スポーツ','キッチン','ハンドメイド','アクセサリー',
    'おもちゃ','ベビー・キッズ'];
    $picked = old('category');
    @endphp

    <div class="form-block">
        <label class="form-label">カテゴリー</label>
        <fieldset class="chips">
            @foreach($categories as $i => $cat)
            <input
                type="radio"
                id="cat-{{ $i }}"
                name="category"
                value="{{ $cat }}"
                class="chip-input visually-hidden"
                {{ $picked === $cat ? 'checked' : '' }}
                required>
            <label class="chip" for="cat-{{ $i }}">{{ $cat }}</label>
            @endforeach
        </fieldset>
        @error('category')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- 状態 --}}
    <div class="form-block">
        <label class="form-label" for="condition">商品の状態</label>
        <div class="select">
            <select id="condition" name="condition" required>
                <option value="">選択してください</option>
                <option value="1" @selected(old('condition')=='1' )>新品</option>
                <option value="2" @selected(old('condition')=='2' )>未使用に近い</option>
                <option value="3" @selected(old('condition')=='3' )>目立った傷や汚れなし</option>
                <option value="4" @selected(old('condition')=='4' )>やや傷や汚れあり</option>
                <option value="5" @selected(old('condition')=='5' )>傷や汚れあり</option>
                <option value="6" @selected(old('condition')=='6' )>全体的に状態が悪い</option>
            </select>
        </div>
        @error('condition')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- 商品名・ブランド名 --}}
    <div class="form-block">
        <label class="form-label" for="title">商品名</label>
        <input id="title" class="form-input" type="text" name="title" value="{{ old('title') }}" required placeholder="例）カーディガン">
        @error('title')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-block">
        <label class="form-label" for="brand">ブランド名（任意）</label>
        <input id="brand" class="form-input" type="text" name="brand" value="{{ old('brand') }}">
    </div>

    {{-- 説明 --}}
    <div class="form-block">
        <label class="form-label" for="description">商品の説明（任意）</label>
        <textarea id="description" class="form-textarea" name="description" rows="5" placeholder="商品の状態、サイズ、色、注意点など">{{ old('description') }}</textarea>
    </div>

    {{-- 価格 --}}
    <div class="form-block">
        <label class="form-label" for="price">販売価格</label>
        <div class="price-row">
            <span class="yen">¥</span>
            <input
                id="price"
                class="form-input price-input"
                type="number"
                name="price"
                value="{{ old('price') }}"
                min="1"
                step="1"
                inputmode="numeric"
                pattern="[0-9]*">
        </div>
        @error('price')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-actions">
        <button class="btn btn--primary btn--block" type="submit">出品する</button>
    </div>
</form>
@endsection