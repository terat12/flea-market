@extends('layouts.app')
@section('title','商品詳細')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/entrance.css') }}">
@endpush

@section('content')
<div class="entrance">
    <div class="entrance-grid">
        <div class="entrance-left">
            
            <div class="thumb-lg">
                @if($product->image_path)
                <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->title }}">
                @endif
            </div>

        </div>

        <div class="entrance-right">
            <h1 class="title">{{ $product->title }}</h1>
            <div class="price">¥{{ number_format($product->price) }} <span class="tax">(税込)</span></div>

            {{-- 購入画面へ 未ログインならログイン/認証へ --}}
            <a class="btn btn--primary w-full" href="{{ route('purchase.checkout', $product->id) }}">購入手続きへ</a>

            <h2 class="section-title">商品の情報</h2>
            <p>ブランド：{{ $product->brand ?? '—' }}</p>
            <p>カテゴリ：
                @if($product->category)
                <span class="badge">{{ $product->category }}</span>
                @else
                <span class="muted">—</span>
                @endif
            </p>
            <p>商品の状態：{{ $product->condition_label }}</p>

            <h2 class="section-title">商品説明</h2>
            @if($product->description)
            <p>{{ $product->description }}</p>
            @else
            <p class="muted">説明はありません。</p>
            @endif

            <h2 class="section-title">商品へのコメント</h2>
            <textarea class="textarea" placeholder="ここにコメントが入ります。"></textarea>
            <div class="form-actions">
                <button class="btn btn--primary" type="button">コメントを送信する</button>
            </div>
        </div>
    </div>
</div>
@endsection