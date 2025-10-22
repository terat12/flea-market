<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Product $product)
    {
        // バリデーション（一時的）
        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        // 保存（ログイン中のユーザーで紐付け）
        $product->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $data['body'],
        ]);

        // そのまま詳細に戻る（JSを使えばよりよいものが作れるのでは？）
        return back();
    }
}
