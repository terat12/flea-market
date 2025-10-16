@if($products->isEmpty())
<p class="muted" style="margin:12px 0;">
    該当する商品はまだありません。
</p>
@else
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
@endif