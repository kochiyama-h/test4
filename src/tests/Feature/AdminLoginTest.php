<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;


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
