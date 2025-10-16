@extends('layouts.app')
@section('title','商品購入')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('content')
<div class="purchase">
    <form class="purchase-main" method="POST" action="{{ route('purchase.complete') }}">
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

        <div class="purchase-section">
            <div class="section-title">支払い方法</div>
            <div class="select">
                <select name="payment" required>
                    <option value="" selected>選択してください</option>
                    <option value="convenience">コンビニ払い</option>
                    <option value="card">カード払い</option>
                </select>
            </div>
            @error('payment')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="purchase-section">
            <div class="section-title">配送先</div>
            <div class="address">
                <div>〒 {{ $user->zipcode ?? '----' }}</div>
                <div>{{ $user->address ?? '住所未設定' }}</div>
                <div>{{ $user->building ?? '' }}</div>
                {{-- 「購入画面 → 送付先の『変更する』 → プロフィール編集 → 保存後に マイページへ」を防止 --}}
                <a class="link"
                    href="{{ route('profile.edit', ['return_to' => route('purchase.checkout', data_get($product,'id'))]) }}">
                    変更する
                </a>
            </div>
        </div>

        <div class="spacer"></div>
        <button class="btn btn--primary" type="submit">購入する</button>
    </form>

    <aside class="purchase-summary">
        <div class="summary-box">
            <div class="row"><span>商品代金</span><span>¥{{ number_format($product->price) }}</span></div>
            <div class="row"><span>支払い方法</span><span>—</span></div>
        </div>
    </aside>
</div>
@endsection