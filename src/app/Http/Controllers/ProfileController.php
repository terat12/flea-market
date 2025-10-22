<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        // ▼ タブを読み取る（規定値は「購入した商品」）
        $tab = $request->query('tab', 'purchased');

        // ▼ Productのコレクション
        if ($tab === 'listed') {
            $items = Product::where('user_id', $user->id)
                ->latest('id')
                ->get();
        } else {
            $items = Order::with(['product:id,title,price,image_path'])
                ->where('user_id', $user->id)
                ->latest('id')
                ->get()
                ->pluck('product')
                ->filter()
                ->values();
        }
            return view('mypage.show', compact('user', 'tab', 'items'));
    }


    public function edit()
    {
        /** @var User $user */
        $user = auth()->user();
        return view('mypage.edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = $path;
        }

        $user->name     = $request->name;
        $user->zipcode  = $request->zipcode;
        $user->address  = $request->address;
        $user->building = $request->building;
        $user->save();

        // --- ↓リダイレクト制御 ---
        
        $returnTo   = (string) $request->input('return_to');
        $firstSetup = $request->boolean('first_setup');

        // 購入画面から来た：購入手続きへ
        if ($returnTo !== '' && str_starts_with($returnTo, url('/'))) {
            return redirect()->to($returnTo)->with('status', 'プロフィールを更新しました');
        }

        // 初回セットアップ：トップ画面へ
        if ($firstSetup) {
            return redirect()->route('products.index')->with('status', 'プロフィールを更新しました');
        }

        // マイページからきた：マイページへ
        return redirect()->route('profile.show')->with('status', 'プロフィールを更新しました');
    }
}
