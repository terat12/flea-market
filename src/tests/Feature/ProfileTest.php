<?php

namespace Tests\Feature;

use Tests\TestCase;                                  // テストの土台
use Illuminate\Foundation\Testing\RefreshDatabase;   // テストごとにDB初期化
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /*＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ 
       ID:13 ユーザー情報取得
       ＜テスト内容＞
       必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）

       ＜テスト手順＞
       1. ユーザーにログインする
       2. プロフィールページ（/mypage）を開く

       ＜期待挙動＞
       ・プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧が正しく表示される
    ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/

public function test_profile_page_shows_user_info_and_lists(): void
{
    // 認証ユーザー(プロフ必須項目を埋めた想定)
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'zipcode'  => '123-4567',
        'address'  => '東京都テスト区1-2-3',
        'building' => 'テストビル',
        'name'     => 'テスト太郎',
    ]);

    // 自分の出品（一覧に出る想定）
    $ownA = Product::factory()->create(['title' => '自分の出品A', 'user_id' => $user->id]);
    $ownB = Product::factory()->create(['title' => '自分の出品B', 'user_id' => $user->id]);

    // 購入した商品（他者出品を購入した想定）
    $other  = User::factory()->create();
    $bought = Product::factory()->create(['title' => '購入品X', 'user_id' => $other->id]);
    DB::table('orders')->insert([
        'user_id'    => $user->id,
        'product_id' => $bought->id,
        'price'      => $bought->price,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    //  購入タブ：ユーザー名、購入品、アバターを確認
    $buy = $this->actingAs($user)->get('/mypage?page=buy');
    $buy->assertStatus(200)
        ->assertSee('テスト太郎')
        ->assertSee('購入品X')
        ->assertSee('avatar');

    //  出品タブ：自分の出品A/Bを確認
    $sell = $this->actingAs($user)->get('/mypage?page=sell');
    $sell->assertStatus(200)
         ->assertSee('自分の出品A')
         ->assertSee('自分の出品B');
}

    /*＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ 
       ID:14 ユーザー情報変更
       ＜テスト内容＞
      変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）

       ＜テスト手順＞
       1. ユーザーにログインする
       2. プロフィール編集ページ（/mypage/profile）を開く

       ＜期待挙動＞
       各項目の初期値がフォームに反映されている
    ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/

    public function test_profile_edit_form_is_prefilled_with_existing_values(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'name'     => '初期ネーム',
            'zipcode'  => '987-6543',
            'address'  => '大阪府テスト市4-5-6',
            'building' => '初期ビル',
        ]);

        $res = $this->actingAs($user)->get('/mypage/profile');

        // 初期値が画面に出ているか
        $res->assertStatus(200)
            ->assertSee('初期ネーム')
            ->assertSee('987-6543')
            ->assertSee('大阪府テスト市4-5-6')
            ->assertSee('初期ビル')
            ->assertSee('avatar'); // プロフィール画像
    }
}
