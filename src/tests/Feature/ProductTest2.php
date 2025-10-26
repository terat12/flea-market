<?php

namespace Tests\Feature;

use Tests\TestCase;                                  // テストの土台
use Illuminate\Foundation\Testing\RefreshDatabase;   // テストごとにDB初期化
use Illuminate\Support\Facades\DB;                   // ピボットや素のINSERTを使いたいとき用
use Illuminate\Support\Str;                          // 255文字テスト用
use App\Models\User;                                 // usersテーブルのモデル
use App\Models\Product;                              // productsテーブルのモデル

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /*＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ 
       ID:5 マイリスト一覧取得
       ・いいねした商品だけが表示される
       ・購入済み商品は「Sold」ラベル（※実装わすれ）
       ・未認証（メール未確認）は開けない想定
    ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/

    // いいねした商品だけが表示される
    public function test_mylist_shows_only_liked_products(): void
    {
        // 認証に必要な状態を作る
        $user = User::factory()->create([
            'email_verified_at' => now(),  // ← verified ミドルウェアを通す
            // 住所等をここで埋める
            'zip'      => '123-4567',
            'address'  => '東京都テスト区1-2-3',
            'building' => 'テストビル',
        ]);

        // 商品を3つ作る
        $p1 = Product::factory()->create(['name' => 'Aりんご']);
        $p2 = Product::factory()->create(['name' => 'Bバナナ']);
        $p3 = Product::factory()->create(['name' => 'Cメロン']);

        // Aりんご と Cメロン だけ「いいね」しておく
        DB::table('likes')->insert([
            ['user_id' => $user->id, 'product_id' => $p1->id, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $user->id, 'product_id' => $p3->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ログイン → マイリストタブ
        $res = $this->actingAs($user)->get('/products?tab=mylist');

        $res->assertStatus(200)
            ->assertSee('Aりんご')  // 見える
            ->assertSee('Cメロン')  // 見える
            ->assertDontSee('Bバナナ');  // 見えない
    }

    // 購入済み商品は「Sold」ラベルが表示される（ただし実装し忘れているので今はただの飾り）
    public function test_mylist_sold_label_is_shown_for_purchased_items(): void
    {

        $user = User::factory()->create(['email_verified_at' => now()]);
        $p   = Product::factory()->create(['name' => 'Soldになるやつ']);

        // いいね
        DB::table('likes')->insert([
            'user_id' => $user->id, 'product_id' => $p->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // 「購入済み」状態を作る
        // ※アプリ側の実装に合わせてordersテーブルのカラムを調整されたい
        DB::table('orders')->insert([
            'user_id'    => $user->id,
            'product_id' => $p->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $res = $this->actingAs($user)->get('/products?tab=mylist');

        // 文字列 "Sold" が出る想定（ラベルの表記が違うなら合わせる）
        $res->assertStatus(200)
            ->assertSee('Sold')
            ->assertSee('Soldになるやつ');
    }

    // 未認証の場合は開けない → 誘導ページ
    public function test_mylist_requires_email_verification(): void
    {
        // 未認証ユーザー
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // 誘導ページへリダイレクトされる想定（/email/verify）
        $this->actingAs($user)
            ->get('/products?tab=mylist')
            ->assertStatus(302)
            ->assertRedirect('/email/verify');
    }

    /*＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ 
       ID:8 いいね機能
       ・押下で登録され、合計値が増える
       ・再度押下で解除され、合計値が減る
    ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/

    // いいね登録をする
    public function test_like_registers_product_and_appears_in_mylist(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $p    = Product::factory()->create(['name' => 'いいね対象']);

        // いいね押下
        $this->actingAs($user)
            ->post("/products/{$p->id}/like")
            ->assertStatus(302); // 押下後はその場でリダイレクト

        // ライクをDBで確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'product_id' => $p->id,
        ]);

        // マイリストに出てくる
        $this->actingAs($user)
            ->get('/products?tab=mylist')
            ->assertStatus(200)
            ->assertSee('いいね対象');
    }

    // いいね解除 → likes が消える
    public function test_like_toggle_off_removes_record_and_disappears_from_mylist(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $p    = Product::factory()->create(['name' => '解除対象']);

        // 登録
        DB::table('likes')->insert([
            'user_id' => $user->id, 'product_id' => $p->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // 解除
        $this->actingAs($user)
            ->delete("/products/{$p->id}/like")
            ->assertStatus(302);

        // likeが消えている
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'product_id' => $p->id,
        ]);

        // マイリストからも消えている
        $this->actingAs($user)
            ->get('/products?tab=mylist')
            ->assertStatus(200)
            ->assertDontSee('解除対象');
    }

    /*＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ 
        ID:9 コメント送信機能
       ・ログイン済みなら送信できて件数が増える
       ・未ログインは送信できない
       ・必須/255文字のバリデーション        
    ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/

    // コメントが保存され、表示される
    public function test_comment_post_success_and_visible_on_detail(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $p    = Product::factory()->create(['name' => 'コメント対象']);

        $payload = ['body' => 'とても良いです！'];

        $res = $this->actingAs($user)
            ->from("/products/{$p->id}")
            ->post("/products/{$p->id}/comments", $payload);

        // 成功 → その場でリダイレクト？
        $res->assertStatus(302);

        // DBにコメントが入っているか
        $this->assertDatabaseHas('comments', [
            'product_id' => $p->id,
            'user_id'    => $user->id,
            'body'       => 'とても良いです！',
        ]);

        // 商品詳細ページに💬が出ている
        $this->get("/products/{$p->id}")
            ->assertStatus(200)
            ->assertSee('とても良いです！');
    }

    // 未認証ユーザーはコメントできず、ログイン画面へ遷移
    public function test_comment_requires_login(): void
    {
        $p = Product::factory()->create();

        $this->post("/products/{$p->id}/comments", ['body' => 'ゲスト投稿'])
            ->assertStatus(302)
            ->assertRedirect('/login');
    }

    // 必須／255文字 → バリデーション
    public function test_comment_validation_required_and_max_255(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $p    = Product::factory()->create();

        // 空 → エラー
        $r1 = $this->actingAs($user)
            ->from("/products/{$p->id}")
            ->post("/products/{$p->id}/comments", ['body' => '']);
        $r1->assertStatus(302)->assertSessionHasErrors(['body']);

        // 256文字以上 → エラー
        $tooLong = Str::repeat('あ', 256);
        $r2 = $this->actingAs($user)
            ->from("/products/{$p->id}")
            ->post("/products/{$p->id}/comments", ['body' => $tooLong]);
        $r2->assertStatus(302)->assertSessionHasErrors(['body']);
    }
}
