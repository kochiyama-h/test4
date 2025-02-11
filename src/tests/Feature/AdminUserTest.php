<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;
    
     //ID14:ユーザー情報取得機能（管理者）
    /**
     *  管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
     */ 

     public function test_admin_name_email()
     {
         // 管理者ユーザー作成（is_admin = 1）
         $admin = User::factory()->create([
             'is_admin' => 1,
         ]);
 
         // 一般ユーザー作成（is_admin = 0）
         $user1 = User::factory()->create([
             'name' => 'aaa',
             'email' => 'aaa@aaa',
             'is_admin' => 0,
         ]);
 
         $user2 = User::factory()->create([
             'name' => 'bbb',
             'email' => 'bbb@bbb',
             'is_admin' => 0,
         ]);

         $admin = User::factory()->create(['is_admin' => 1])->first();
 
         // 管理者でログイン
         $this->actingAs($admin);
 
         // スタッフ一覧ページへアクセス
         $response = $this->get(route('admin.staff.list'));
 
         // 正しく表示されているか確認
         $response->assertStatus(200)
             ->assertSee('aaa')
             ->assertSee('aaa@aaa')
             ->assertSee('bbb')
             ->assertSee('bbb@bbb');
     }

    
    /**
     *  ユーザーの勤怠情報が正しく表示される
     */ 
    
     public function test_user_attendance_date()
     {

        // 管理者ユーザー作成（is_admin = 1）
        $admin = User::factory()->create([
            'is_admin' => 1,
        ]);

        // 一般ユーザー作成（is_admin = 0）
        $user = User::factory()->create([
            'name' => 'aaa',
            'email' => 'aaa@aaa',
            'is_admin' => 0,
        ]);

        // 現在の日付を取得
        $currentDate = Carbon::now();

        // テスト用の勤怠データを作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => $currentDate->format('Y-m-d'),
            'clock_in' => $currentDate->format('Y-m-d') . ' 09:00:00',
            'clock_out' => $currentDate->format('Y-m-d') . ' 18:00:00',
            'status' => 'left',
        ]);

        // 休憩時間を追加
        BreakTime::create([
            'attendance_record_id' => $attendance->id,
            'start_time' => $currentDate->format('Y-m-d') . ' 12:00:00',
            'end_time' => $currentDate->format('Y-m-d') . ' 13:00:00',
        ]);


        $admin = User::factory()->create(['is_admin' => 1])->first();
 
         // 管理者でログイン
         $this->actingAs($admin);
 
         // スタッフ一覧ページへアクセス
         $response = $this->get(route('admin.staff.attendance', ['id' => $user->id]));


         $response
            ->assertSee($currentDate->format('m/d'))
             ->assertSee('09:00')
             ->assertSee('18:00')
             ->assertSee('01:00')
             ->assertSee('08:00');


    }

    /**
     *  「前月」を押下した時に表示月の前月の情報が表示される
     */ 

     public function test_previous_month()
    {

     // 管理者ユーザー作成（is_admin = 1）
     $admin = User::factory()->create([
        'is_admin' => 1,
    ]);

    // 一般ユーザー作成（is_admin = 0）
    $user = User::factory()->create([
        'name' => 'aaa',
        'email' => 'aaa@aaa',
        'is_admin' => 0,
    ]);

    $previousMonthDate = Carbon::now()->subMonth()->startOfMonth()->addDays(4);
    
    // テスト用の勤怠データを作成
    $attendance = AttendanceRecord::create([
        'user_id' => $user->id,
        'date' => $previousMonthDate->format('Y-m-d'),
        'clock_in' => $previousMonthDate->format('Y-m-d') . ' 09:00:00',
        'clock_out' => $previousMonthDate->format('Y-m-d') . ' 18:00:00',
        'status' => 'left',
    ]);

    // 休憩時間を追加
    BreakTime::create([
        'attendance_record_id' => $attendance->id,
        'start_time' => $previousMonthDate->format('Y-m-d') . ' 12:00:00',
        'end_time' => $previousMonthDate->format('Y-m-d') . ' 13:00:00',
    ]);

    $admin = User::factory()->create(['is_admin' => 1])->first();

    $previousMonth = $previousMonthDate->format('Y-m');
 
      // 管理者でログイン
       $this->actingAs($admin);

        // スタッフ一覧ページへアクセス
        $response = $this->get(route('admin.staff.attendance', ['id' => $user->id]));
 
      


       

       $response
            ->assertSee($previousMonthDate->format('m/d'))
             ->assertSee('09:00')
             ->assertSee('18:00')
             ->assertSee('01:00')
             ->assertSee('08:00');

        }

       




     /**
     *  「翌月」を押下した時に表示月の前月の情報が表示される
     */ 

    
}



