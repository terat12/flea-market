<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Product $product)
    {
        // バリデーション
        $data = $request->validated();

        // 保存（ログイン中のユーザーで紐付け）
        $product->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $data['body'],
        ]);

        // そのまま詳細に戻る（JSを使えばよりよいものが作れるのでは？）
        return back();
    }
}
