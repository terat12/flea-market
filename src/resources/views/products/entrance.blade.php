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

        {{-- ここから右カラム --}}
        <div class="entrance-right">
            <h1 class="title">{{ $product->title }}</h1>

            {{-- ブランドをタイトル下に表示する --}}
            @if($product->brand)
            <div class="brand">{{ $product->brand }}</div>
            @endif

            <div class="price">¥{{ number_format($product->price) }} <span class="tax">(税込)</span></div>

            {{-- ★/💬 --}}
            <div class="meta-row">

                {{-- （★/☆）＋合計 --}}
                <div class="like">
                    @php($liked = $isLiked ?? false)
                    @auth
                    @if($liked)
                    <form method="POST" action="{{ route('likes.destroy', $product) }}">
                        @csrf @method('DELETE')
                        <button type="submit" aria-pressed="true" aria-label="マイリストから外す" title="マイリストから外す"></button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('likes.store', $product) }}">
                        @csrf
                        <button type="submit" aria-pressed="false" aria-label="マイリストに追加" title="マイリストに追加"></button>
                    </form>
                    @endif
                    @else
                    <a href="{{ route('login') }}" aria-label="ログインしていいね" title="ログインしていいね"></a>
                    @endauth
                    <span>{{ $product->liked_users_count ?? 0 }}</span>
                </div>

                {{-- （💬）＋合計 --}}
                <a href="#comments" class="comment-link" aria-label="コメント欄へ">
                    <span aria-hidden="true"></span>
                    <span>{{ $product->comments_count ?? 0 }}</span>
                </a>
            </div>

            {{-- 購入画面へ --}}
            <a class="btn btn--primary w-full" href="{{ route('purchase.checkout', $product->id) }}">購入手続きへ</a>

            <h2 class="section-title">商品説明</h2>
            @if($product->description)
            <p>{{ $product->description }}</p>
            @else
            <p class="muted">説明はありません。</p>
            @endif

            <h2 class="section-title">商品の情報</h2>
            <p>カテゴリ：
                @if($product->category)
                <span class="badge">{{ $product->category }}</span>
                @else
                <span class="muted">—</span>
                @endif
            </p>
            <p>商品の状態：{{ $product->condition_label }}</p>

            {{-- コメント一覧見出し --}}
            <h2 class="section-title">コメント（{{ $product->comments_count ?? $product->comments->count() }}）</h2>
            <div class="comment-list">
                @forelse($product->comments->take(20) as $c)
                <div class="comment-row" style="margin-bottom:12px;">
                    <strong>{{ $c->user->name }}</strong>
                    <span class="muted" style="margin-left:8px;">{{ $c->created_at->format('Y/m/d H:i') }}</span>
                    <p style="margin:6px 0 0;">{{ e($c->body) }}</p>
                </div>
                @empty
                <p class="muted">まだコメントはありません。</p>
                @endforelse
            </div>

            {{-- 入力フォーム見出し --}}
            <h2 id="comments" class="section-title">商品へのコメント</h2>

            @auth
            <form method="POST" action="{{ route('products.comments.store', $product) }}" class="mt-16">
                @csrf
                <textarea name="body" rows="3" class="textarea" required placeholder="コメントを入力してください。"></textarea>
                @error('body') <div class="error">{{ $message }}</div> @enderror
                <div class="form-actions">
                    {{-- テキストエリアと同じ幅にする --}}
                    <button class="btn btn--primary w-full" type="submit">コメントを送信する</button>
                </div>
            </form>
            @else
            <a class="btn btn--primary" href="{{ route('login') }}">ログインしてコメントを書く</a>
            @endauth
        </div>
    </div> {{-- /.entrance-grid --}}
</div> {{-- /.entrance --}}
@endsection