<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\LikeController;

// （ログイン時のみ）
Route::middleware(['auth', 'verified'])->group(function () {
    // 商品一覧・詳細・出品
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); // ★追加
    Route::get('/products/{id}', [ProductController::class, 'entrance'])->name('products.entrance');

    Route::get('/purchase/checkout/{product}', [PurchaseController::class, 'checkout'])
        ->name('purchase.checkout');

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




// トップ（認証状態で分岐）
Route::get('/', fn() => auth()->check()
    ? redirect()->route('products.index')
    : redirect()->route('login'));

// メール認証（Fortifyでも必要になりそう）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/products');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ★近いうちに消す★（AIが足せというので一応）動作確認用プレーンビュー
Route::get('/test', fn() => view('index'));

// ★近いうちに消す★（未ログインでも閲覧できる）一覧・詳細（とりあえず表示させる用）
Route::get('/g/products', [ProductController::class, 'index'])->name('products.index.guest');
Route::get('/g/products/{id}', [ProductController::class, 'entrance'])->name('products.entrance.guest');
