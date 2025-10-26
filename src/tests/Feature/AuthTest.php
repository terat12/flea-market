<?php

namespace Tests\Feature;

use Tests\TestCase;                                // テストの土台となるクラス
use Illuminate\Foundation\Testing\RefreshDatabase; // DBを毎テスト終了時に初期化する
use Illuminate\Support\Facades\Hash;               // パスワードをハッシュ化する「ファサード」→安全な文字列
use App\Models\User;                               // usersテーブルに対応する、Eloquentモデル

class AuthTest extends TestCase
{
    use RefreshDatabase;
    // ↑これが、テスト実行前にテスト用DBへ migrate して、各テストを散らかすことなく実行できるようにリフレッシュしてくれる。

    // ID:1 簡易登録機能
    public function test_register_creates_user_and_redirects() : void
    {
        // /register への入力値）
        $response = $this->post('/register', [
            'name'                  => 'new user',
            'email'                 => 'newuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // usersテーブルに該当レコードが出来たかを確認する
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);

        // 登録直後はログイン状態（Fortify）
        $this->assertAuthenticated();

        // 成功後にリダイレクト
        $response->assertRedirect('/products')->assertStatus(302);
    }

    // ID:1 簡易登録機能
    public function test_register_shows_japanese_validation_messages() : void
    {
        // 1) 未入力
        $r1 = $this->from('/register')->post('/register', [
            'name'                  => '',
            'email'                 => '',
            'password'              => '',
            'password_confirmation' => '',
        ]);
        $r1->assertStatus(302)->assertRedirect('/register'); // いったん戻る
        $html1 = $this->followRedirects($r1);
        $html1->assertSee('お名前を入力してください');
        $html1->assertSee('メールアドレスを入力してください');
        $html1->assertSee('パスワードを入力してください'); 

        // 2) 形式不正（emailがメール形式じゃない）＋ パスワード不一致
        $r2 = $this->from('/register')->post('/register', [
            'name'                  => 'abc',
            'email'                 => 'not-an-email',
            'password'              => 'password123',
            'password_confirmation' => 'different',
        ]);
        $r2->assertStatus(302)->assertRedirect('/register');
        $html2 = $this->followRedirects($r2);
        $html2->assertSee('メールアドレスはメール形式で入力してください');
        $html2->assertSee('パスワードと一致しません');

        // 3) 文字数（パスワードが7文字で min 8 に引っかかる）
        $r3 = $this->from('/register')->post('/register', [
            'name'                  => 'abc',
            'email'                 => 'a@b.com',
            'password'              => '1234567',
            'password_confirmation' => '1234567',
        ]);
        $r3->assertStatus(302)->assertRedirect('/register');
        $html3 = $this->followRedirects($r3);
        $html3->assertSee('パスワードは8文字以上で入力してください');

        // 失敗系はいずれも未ログインのまま
        $this->assertGuest();
    }

    // ID:2 ログイン機能（その１）
    public function test_login_with_valid_credentials_redirects_to_profile_edit() : void
    {
        // ログイン可能なユーザーをDBへ
        $user = User::factory()->create([
            'email'    => 'login@example.com',
            'password' => Hash::make('secretpass'), // ← ハッシュ文にして保存
        ]);

        // /login に必要な入力
        $response = $this->post('/login', [
            'email'    => 'login@example.com',
            'password' => 'secretpass',
        ]);

        // 認証された人が $user であることを検証する
        $this->assertAuthenticatedAs($user);

        // 成功 → ホーム画面へ
        $response->assertRedirect('/products')->assertStatus(302);
    }

    // ID:2 ログイン機能（その２）
    public function test_login_shows_japanese_validation_and_auth_error_messages() : void
    {
        //  未入力だと戻ってきてエラー文言が出る
        $lr1 = $this->from('/login')->post('/login', [
            'email'    => '',
            'password' => '',
        ]);
        $lr1->assertStatus(302)->assertRedirect('/login');
        $html1 = $this->followRedirects($lr1);
        $html1->assertSee('メールアドレスを入力してください');
        $html1->assertSee('パスワードを入力してください');

        //  emailがメール形式じゃない
        $lr2 = $this->from('/login')->post('/login', [
            'email'    => 'not-an-email',
            'password' => 'x',
        ]);
        $lr2->assertStatus(302)->assertRedirect('/login');
        $html2 = $this->followRedirects($lr2);
        $html2->assertSee('メールアドレスはメール形式で入力してください');

        //  認証失敗
        User::factory()->create([
            'email'    => 'foo@example.com',
            'password' => Hash::make('correct-pass'),
        ]);
        $lr3 = $this->from('/login')->post('/login', [
            'email'    => 'foo@example.com',
            'password' => 'wrong-pass',
        ]);
        $lr3->assertStatus(302)->assertRedirect('/login');
        $html3 = $this->followRedirects($lr3);
        $html3->assertSee('ログイン情報が登録されていません');

        // 認証が通っていない → ゲスト
        $this->assertGuest();
    }

    // ID:3 ログアウト機能
    public function test_logout_logs_user_out() : void
    {
        // ログイン状態を準備
        $user = User::factory()->create();
        $this->actingAs($user); 

        $response = $this->post('/logout');

        // ログアウト → ゲスト
        $this->assertGuest();

        // 成功 → 302 リダイレクト
        $response->assertStatus(302);
    }
}
