@extends('layouts.app')
@section('title','購入が完了しました')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('content')
<div class="purchase">
    <div class="purchase-main">
        <h2 class="section-title">購入が完了しました</h2>

        @isset($order)
        <p>注文番号：{{ $order->id }}</p>
        @endisset

        <div class="purchase-section">
            <div class="section-title">商品</div>
            <div class="item-title">{{ data_get($product,'title') }}</div>
            <div class="item-price">¥{{ number_format((int) data_get($product,'price')) }}</div>
        </div>

        <div class="purchase-section">
            <div class="section-title">支払い方法</div>
            <p>{{ $paymentLabel ?? '—' }}</p>
        </div>

        @isset($order)
        <div class="purchase-section">
            <div class="section-title">配送先</div>
            <div class="address">
                <div>〒 {{ $order->shipping_zip }}</div>
                <div>{{ $order->shipping_address }}</div>
                <div>{{ $order->shipping_building }}</div>
            </div>
        </div>
        @endisset

        <div class="complete-actions">
            <a href="{{ route('products.index') }}" class="btn btn--primary">トップへ戻る</a>

            @isset($order)
            <a href="{{ route('products.entrance', $order->product_id) }}" class="btn">商品ページへ</a>
            @else
            <a href="{{ route('products.entrance', data_get($product,'id')) }}" class="btn">商品ページへ</a>
            @endisset

            <a href="{{ route('profile.show') }}" class="btn">マイページ</a>
        </div>
    </div>

    <aside class="purchase-summary">
        <div class="summary-box">
            <div class="row"><span>お支払い金額</span><span>¥{{ number_format((int) data_get($product,'price')) }}</span></div>
        </div>
    </aside>
</div>
@endsection