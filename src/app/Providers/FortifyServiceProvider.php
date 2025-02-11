<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ユーザー作成アクションの登録
        Fortify::createUsersUsing(CreateNewUser::class);

        // 登録画面
        Fortify::registerView(fn () => view('auth.register'));

        // ログイン画面のカスタマイズ
        Fortify::loginView(function () {
            return \Illuminate\Support\Facades\Request::is('admin/login') 
                ? view('auth.admin_login') 
                : view('auth.login');
        });

        // ログイン試行のレートリミッター
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}