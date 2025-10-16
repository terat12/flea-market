<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Product $product, Request $request)
    {
        $request->user()->likes()->syncWithoutDetaching([$product->id]);
        return back();
    }
    public function destroy(Product $product, Request $request)
    {
        $request->user()->likes()->detach($product->id);
        return back();
    }
}