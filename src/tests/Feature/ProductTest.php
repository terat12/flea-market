<?php

namespace Tests\Feature;

use Tests\TestCase;                                  // テストの土台
use Illuminate\Foundation\Testing\RefreshDatabase;   // テストごとにDB初期化
use App\Models\Product;                              // productsテーブルのモデル

class ProductTest extends TestCase
{
    use RefreshDatabase;

    // ID:4 商品一覧（おすすめタブ）の表示
    public function test_product_index_recommend_tab_as_guest(): void
    {
        // テスト用の商品を作成
        Product::factory()->create(['title' => 'おすすめA']);
        Product::factory()->create(['title' => 'おすすめB']);

        // ゲスト用の一覧にアクセスできるか（/g/products）
        $res = $this->get('/g/products');

        // 200で表示でき、作った商品の名前が出ていること
        $res->assertStatus(200)
            ->assertSee('おすすめA')
            ->assertSee('おすすめB');
    }

    // ID:6 商品検索機能
    public function test_product_search_by_partial_title_on_guest_index(): void
    {
        Product::factory()->create(['title' => 'りんごジュース']);
        Product::factory()->create(['title' => 'バナナスムージー']);

        // りん で検索 → りんごは出る・バナナは出ない
        $res = $this->get('/g/products?q=りん');

        $res->assertStatus(200)
            ->assertSee('りんご')     // 一部でも含まれていれば検索可？
            ->assertDontSee('バナナ'); // 検索にヒットしないものは非表示
    }

    // ID:7 商品詳細情報取得
    public function test_guest_can_view_product_detail_with_multiple_categories(): void
    {
        // カテゴリ：「トップス」「ジャケット」の2つ
        $product = Product::factory()->create([
            'title'    => '秋コーデのコート',
            'brand'    => 'ACME',
            'category' => 'トップス、ジャケット',
            'price'    => 12000,
        ]);

        // ゲスト用の詳細が開ける
        $response = $this->get("/g/products/{$product->id}");

        // 画面が開ける
        $response->assertOk();

        // タイトルやブランド名など基本情報が見える
        $response->assertSee(e($product->title));
        if ($product->brand) {
            $response->assertSee(e($product->brand));
        }

        // 複数カテゴリの表示確認：それぞれのカテゴリ名が画面に出ているか
        $response->assertSee('トップス');
        $response->assertSee('ジャケット');
    }
}