<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //管理者ユーザー
        $param = [
                 'name' => 'admin_user',
                 'email' => 'admin@admin',
                 'password' => Hash::make('adminadmin'), // パスワードをハッシュ化
                 'is_admin' => 1,
                 'created_at' => now(),
                 'updated_at' => now()
               ];
               DB::table('users')->insert($param);



         //一般ユーザー
         $param = [
            'name' => 'user',
            'email' => 'user@user',
            'password' => Hash::make('useruser'), // パスワードをハッシュ化
            'is_admin' => 0,
            'created_at' => now(),
            'updated_at' => now()
          ];
          DB::table('users')->insert($param);
    }
}
