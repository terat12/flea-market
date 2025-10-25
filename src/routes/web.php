<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\LikeController;


// （ログイン時のみ）
Route::middleware(['auth', 'verified', 'profile.completed'])->group(function () {
    // 商品一覧・詳細・出品
    Route::get('/', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

    Route::get('/item/{id}', [ProductController::class, 'entrance'])->name('products.entrance');

    // コメント機能
    Route::post('/products/{product}/comments', [CommentController::class, 'store'])
        ->name('products.comments.store');

    // 購入画面・住所変更
    Route::get('/purchase/{product}', [PurchaseController::class, 'checkout'])
        ->name('purchase.checkout');
    Route::get('/purchase/address/{product}', fn() => redirect()->route('profile.edit'))
        ->name('purchase.address');

    Route::post('/purchase/complete', [PurchaseController::class, 'complete'])
        ->name('purchase.complete');

    // マイページ
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 出品の保存
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    // マイリスト関連
    Route::post('/products/{product}/like',  [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/products/{product}/like', [LikeController::class, 'destroy'])->name('likes.destroy');
});



// メール認証
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



// ゲスト用
Route::get('/g/products', [ProductController::class, 'index'])->name('products.index.guest');
Route::get('/g/products/{id}', [ProductController::class, 'entrance'])->name('products.entrance.guest');
