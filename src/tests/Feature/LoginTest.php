<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;


    //テストID２:ログイン認証機能（一般ユーザー）
     /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_login_requires_email()
    {
        
        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => 0
        ]);

        
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_login_requires_password()
    {
        
        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => 0
        ]);

       
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }


    /**
     * 登録内容と一致しない場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_wrong_credentials()
    {
        
        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => 0
        ]);

        
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        
        $response->assertSessionHasErrors('email');
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません。']);

        
    }

    





}
