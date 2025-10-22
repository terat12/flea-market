@extends('layouts.app')
@section('title','商品購入')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@php
$paymentLabel = ['convenience' => 'コンビニ払い', 'card' => 'カード払い'];
$selected = old('payment');
@endphp

@section('content')
<div class="purchase">
    <form id="purchase-form" class="purchase-main" method="POST" action="{{ route('purchase.complete') }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div class="purchase-item">
            <div class="thumb">
                @if($product->image_path)
                <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->title }}">
                @endif
            </div>
            <div>
                <div class="item-title">{{ $product->title }}</div>
                <div class="item-price">¥{{ number_format($product->price) }}</div>
            </div>
        </div>

        {{-- 支払い方法 --}}
        <div class="purchase-section">
            <div class="section-title">支払い方法</div>
            <div class="select">
                <select name="payment" required>
                    <option value="" {{ $selected ? '' : 'selected' }}>選択してください</option>
                    <option value="convenience" {{ $selected === 'convenience' ? 'selected' : '' }}>コンビニ払い</option>
                    <option value="card" {{ $selected === 'card' ? 'selected' : '' }}>カード払い</option>
                </select>
            </div>
            @error('payment')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="purchase-section">
            <div class="section-title-row">
                <div class="section-title">配送先</div>
                <a class="link"
                    href="{{ route('profile.edit', ['return_to' => route('purchase.checkout', data_get($product,'id'))]) }}">
                    変更する
                </a>
            </div>

            <div class="address">
                <div>〒 {{ $user->zipcode ?? '----' }}</div>
                <div>{{ $user->address ?? '住所未設定' }}</div>
                <div>{{ $user->building ?? '' }}</div>
            </div>
        </div>
    </form>

    {{-- 右側（支払方法を連動する） --}}
    <aside class="purchase-summary">
        <div class="summary-box">
            <div class="row"><span>商品代金</span><span>¥{{ number_format($product->price) }}</span></div>
            <div class="row"><span>支払い方法</span><span data-summary-payment>{{ $paymentLabel[$selected] ?? '—' }}</span></div>
        </div>

        {{-- 送信ボタンは右カラムの下 --}}
        <button type="submit" form="purchase-form" class="btn btn--primary w-full">購入する</button>
    </aside>
</div>
@endsection