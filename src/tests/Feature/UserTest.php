<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    //テストID1:認証機能（一般ユーザー）
     /**
     * 名前が未入力の場合、バリデーションメッセージが表示される
     */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * パスワードが8文字未満の場合、バリデーションメッセージが表示される
     */
    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * パスワードが一致しない場合、バリデーションメッセージが表示される
     */
    public function test_password_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors(['password_confirmation' => 'パスワードと一致しません']);
    }

    /**
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }



    /**
     * フォームに内容が入力されていた場合、データが正常に保存される
     */
    public function test_user_can_register_successfully()
    {
        // テスト用データ
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123', 
            
        ];
        
       $this->post('/register', $userData);
        
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        
    }

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

    //テストID３:ログイン認証機能（管理者）
     /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */

     public function test_admin_login_requires_email()
    {
        
        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password1234'),
            'is_admin' => 1
        ]);

        
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password1234',
        ]);

        
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }


     /**
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_admin_login_requires_password()
    {
        
        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password1234'),
            'is_admin' => 1
        ]);

       
        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }



     /**
     * 登録内容と一致しない場合、バリデーションメッセージが表示される
     */
    public function test_admin_login_fails_with_wrong_credentials()
    {
        
        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password1234'),
            'is_admin' => 1
        ]);

        
        $response = $this->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'password1234',
        ]);

        
        $response->assertSessionHasErrors('email');
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません。']);

        
    }





}
