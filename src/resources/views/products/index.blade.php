@extends('layouts.app')
@section('title','商品一覧')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@section('content')
@php
// ゲスト一覧ならゲスト用の詳細ルートへ
$detailRoute = request()->is('g/*')
? 'products.entrance.guest'
: (auth()->check() ? 'products.entrance' : 'products.entrance.guest');
@endphp

<div class="product-index">
    @php
    // 既存の検索など他クエリを維持したまま tab だけを差し替える
    $qs = request()->except('tab');
    @endphp

    <div class="tabs">
        <a href="{{ route('products.index', array_merge($qs, ['tab' => 'recommend'])) }}"
            class="tab {{ $tab === 'recommend' ? 'tab--active' : '' }}">おすすめ</a>
        <a href="{{ route('products.index', array_merge($qs, ['tab' => 'likes'])) }}"
            class="tab {{ $tab === 'likes' ? 'tab--active' : '' }}">マイリスト</a>
    </div>

    @if($products->isEmpty())
    <p class="mt-16 muted">表示できる商品がありません。</p>
    @else
    <div class="grid">
        @foreach($products as $p)
        <a class="card" href="{{ route($detailRoute, $p->id) }}">
            <div class="card-thumb">
                @if($p->image_path)
                <img src="{{ asset('storage/'.$p->image_path) }}" alt="{{ $p->title }}">
                @endif
            </div>
            <div class="card-title">{{ $p->title }}</div>
            <div class="card-price">¥{{ number_format($p->price) }}</div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- ページネーションは今回は非表示（要件外） --}}
    {{-- <div class="mt-16">{{ $products->withQueryString()->links() }}
</div> --}}
</div>
@endsection