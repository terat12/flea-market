<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{

    public function checkout(Product $product)
    {
        return view('purchase.checkout', [
            'product' => $product,
            'user'    => Auth::user(),
        ]);
    }


    public function complete(PurchaseRequest $request)
    {
        $validated = $request->validated(); // バリデーション

        $user = Auth::user();

        // 配送先チェック（未設定ならプロフィール編集へ）
        if (empty($user->zipcode) || empty($user->address)) {
            return redirect()->route('profile.edit')
                ->with('error', '配送先が未設定です。プロフィールから設定してください。');
        }

        $product = Product::findOrFail($validated['product_id']);

        // 最小限の注文作成（ひとまず）
        $order = Order::create([
            'user_id'           => $user->id,
            'product_id'        => $product->id,
            'price'             => $product->price,
            'payment_method'    => $validated['payment'],
            'status'            => 'completed',
            'shipping_zip'      => $user->zipcode ?? '',
            'shipping_address'  => $user->address ?? '',
            'shipping_building' => $user->building ?? '',
        ]);

        $paymentLabel = $validated['payment'] === 'card' ? 'カード払い' : 'コンビニ払い';

        return view('purchase.complete', [
            'order'        => $order,
            'product'      => $product,
            'paymentLabel' => $paymentLabel,
        ]);
    }

    public function history()
    {
        $orders = Order::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('mypage.purchases', compact('orders'));
    }
}
