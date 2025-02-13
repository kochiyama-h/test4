<?php

namespace App\Http\Controllers;


use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AdminLoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{




    //会員登録
    public function register(RegisterRequest $request)
    {
        // ユーザーの作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // パスワードをハッシュ化
        ]);

        // 自動ログイン
        Auth::login($user);

        
        return redirect()->route('attendance.index');
        
        
    }


    //ログイン
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password'); 

        Auth::attempt($credentials);
       
        return redirect()->route('attendance.index');
        
    }

    //ログアウト
    public function logout(Request $request)
    {
        Auth::logout(); 
        return redirect('/login'); 
    }


    //管理者ログイン
    public function adminLogin(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
       
        Auth::attempt($credentials);

        // ログインユーザーが管理者かチェック
        if (Auth::check() && Auth::user()->is_admin === 1) {
            return redirect()->route('admin.attendance.list');
        }
        
    }

    //管理者ログアウト
    public function adminLogout(Request $request)
    {
        Auth::logout(); 
        return redirect('admin/login'); 
    }
}