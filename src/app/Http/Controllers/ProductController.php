<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $products = Product::query()
            ->when( $q, fn ($w) => $w
            ->where( fn($x) => $x
                   -> where('title', 'like', "%{$q}%")
                   -> orWhere('brand', 'like', "%{$q}%")
                   -> orWhere('category', 'like', "%{$q}%")
                   )
            )
            ->latest()
            ->paginate(12);

        return view('products.index', compact('products'));
    }

    public function entrance($id)
    {
        $product = Product::findOrFail($id);
        return view('products.entrance', compact('product'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // 画像アップロード（必要）
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return redirect()
            ->route('products.entrance', $product->id)
            ->with('status', '出品を登録しました');
    }
    
    public function create()
    {
        return view('products.create'); // 出品フォームへ
    }

}