<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q   = trim((string) $request->query('q', ''));
        $tab = $request->query('tab', 'recommend');

        if ($tab === 'likes' && $request->user()) {
            $products = $request->user()->likes()
                ->when($q !== '', function ($w) use ($q) {
                    $w->where(function ($x) use ($q) {
                        $x->where('title', 'like', "%{$q}%")
                            ->orWhere('brand', 'like', "%{$q}%")
                            ->orWhere('category', 'like', "%{$q}%");
                    });
                })
                ->orderByDesc('likes.created_at')
                ->take(12)
                ->get();
        } else {
            $tab = 'recommend';

            $products = Product::query()
                ->when($q !== '', function ($w) use ($q) {
                    $w->where(function ($x) use ($q) {
                        $x->where('title', 'like', "%{$q}%")
                            ->orWhere('brand', 'like', "%{$q}%")
                            ->orWhere('category', 'like', "%{$q}%");
                    });
                })
                ->latest()
                ->take(12)
                ->get();
        }

        return view('products.index', compact('products', 'tab'));
    }

    public function entrance($id)
    {
    // いいね合計数を出す
    $product = Product::withCount(['likedUsers', 'comments'])->findOrFail($id);

    // 一覧表示用に事前ロード
    $product->load(['comments.user']); 

    // ログイン中なら、自分がいいね済みかを判定する
    $isLiked    = auth()->check() ? $product->isLikedBy(auth()->user()) : false;

    // withCountの結果（liked_users_count／いいねの数）をビューへ渡す
    $likesCount = $product->liked_users_count;
    return view('products.entrance', compact('product', 'isLiked'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // 画像
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $cats = (array) $request->input('categories', []);

        // 先頭末尾の空白を除去
        $cats = array_map(
            fn($s) => preg_replace('/^\pZ+|\pZ+$/u', '', (string) $s),
            $cats
        );

        // 空・重複を排除
        $cats = array_values(array_unique(array_filter($cats)));

        // 「、」で1本の文字列として保存
        $data['category'] = implode('、', $cats);
        unset($data['categories']);

        $data['user_id'] = auth()->id();
        
        $product = Product::create($data);

        return redirect()
            ->route('products.entrance', $product->id)
            ->with('status', '出品を登録しました');
    }

    public function create()
    {
        return view('products.create');
    }
}
