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
        $tab  = $request->get('tab', 'listed'); 

        if ($tab === 'purchased') {
            // 購入した商品（Order→product）
            $items = Order::with(['product:id,title,price,image_path'])
                ->where('user_id', $user->id)
                ->latest('id')
                ->get()
                ->pluck('product')
                ->filter()   // 念のため null 除去
                ->values();
        } else {
            // 出品した商品（products.user_id が無ければ空に）
            $items = Schema::hasColumn('products', 'user_id')
                ? Product::where('user_id', $user->id)->latest('id')->get()
                : collect();
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
        $user->save(); //これでもう赤線が出なくなる

        $returnTo = (string) $request->input('return_to');
        if ($returnTo !== '' && str_starts_with($returnTo, url('/'))) {
            return redirect()->to($returnTo)->with('status', 'プロフィールを更新しました');
        }
        return redirect()->route('profile.show')->with('status', 'プロフィールを更新しました');
    }
}
