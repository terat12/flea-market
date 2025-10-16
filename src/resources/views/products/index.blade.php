@extends('layouts.app')
@section('title','商品一覧')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/index.css') }}"> @endpush

@section('content')
<div class="product-index">
    <div class="tabs">
        <a href="#" class="tab tab--active">おすすめ</a>
        <a href="#" class="tab">マイリスト</a>
    </div>

    <div class="grid">
        @foreach($products as $p)
        <a class="card" href="{{ route('products.entrance', $p->id) }}">

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
    <div class="mt-16">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection