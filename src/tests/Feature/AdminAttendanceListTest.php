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

class AdminAttendanceListTest extends TestCase
{
    //ID12:勤怠一覧情報取得機能（管理者）
    /**
     *  その日になされた全ユーザーの勤怠情報が正確に確認できる
     */ 

     public function test_all_user_attendance()
    {
        // 管理者ユーザーを作成
    $admin = User::factory()->create([
        'is_admin' => 1,
    ]);

    // 一般ユーザーを作成
    $user = User::factory()->create([
        'is_admin' => 0,
    ]);

    // 現在の日付を取得
    $currentDate = Carbon::now();

    // 勤怠データを作成
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



    // 管理者ユーザーでログイン
    $this->actingAs($admin)
        ->get('/admin/attendance/list')
        ->assertStatus(200)
        ->assertSee($user->name)
        ->assertSee($currentDate->format('Y年m月d日'))
        ->assertSee('09:00')
        ->assertSee('18:00')
        ->assertSee('01:00')
        ->assertSee('08:00');
}

    /**
     *  遷移した際に現在の日付が表示される
     */ 
    public function test_current_date()
    {
        // 管理者ユーザーを作成
        $admin = User::factory()->create([
            'is_admin' => 1,
        ]);

        // 現在の日付を取得
        $currentDate = Carbon::now();

        $admin = User::factory()->create(['is_admin' => 1])->first();



        // 管理者ユーザーでログイン
        $this->actingAs($admin)
          ->get('/admin/attendance/list')
          ->assertStatus(200)            
          ->assertSee($currentDate->format('Y年m月d日'));

    }

    /**
     *  「前日」を押下した時に前の日の勤怠情報が表示される
     */
    public function test_previous_date()
    {
            // 管理者ユーザーを作成
            $admin = User::factory()->create([
                'is_admin' => 1,
            ]);

            // 一般ユーザーを作成
            $user = User::factory()->create([
                'is_admin' => 0,
            ]);

            $yesterday = Carbon::yesterday(); // 前日の日付

            $attendance = AttendanceRecord::create([
                'user_id' => $user->id,
                'date' => $yesterday->format('Y-m-d'),
                'clock_in' => $yesterday->format('Y-m-d') . ' 09:00:00',
                'clock_out' => $yesterday->format('Y-m-d') . ' 18:00:00',
                'status' => 'left',
            ]);
        
            // 休憩時間を追加
            BreakTime::create([
                'attendance_record_id' => $attendance->id,
                'start_time' => $yesterday->format('Y-m-d') . ' 12:00:00',
                'end_time' => $yesterday->format('Y-m-d') . ' 13:00:00',
            ]);

            $admin = User::factory()->create(['is_admin' => 1])->first();

            $this->actingAs($admin)
            ->get('/admin/attendance/list')
            ->assertStatus(200);

            $this->get('/admin/attendance/list?date=' . $yesterday)
            ->assertStatus(200)
            ->assertSee($yesterday->format('Y年m月d日'))
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('01:00')
            ->assertSee('08:00');



    }


    /**
     *  「翌日」を押下した時に次の日の勤怠情報が表示される
     */
    public function test_tommorow_date()
    {
            // 管理者ユーザーを作成
            $admin = User::factory()->create([
                'is_admin' => 1,
            ]);

            // 一般ユーザーを作成
            $user = User::factory()->create([
                'is_admin' => 0,
            ]);

            $tomorrow = Carbon::tomorrow(); 

            $attendance = AttendanceRecord::create([
                'user_id' => $user->id,
                'date' => $tomorrow->format('Y-m-d'),
                'clock_in' => $tomorrow->format('Y-m-d') . ' 09:00:00',
                'clock_out' => $tomorrow->format('Y-m-d') . ' 18:00:00',
                'status' => 'left',
            ]);
        
            // 休憩時間を追加
            BreakTime::create([
                'attendance_record_id' => $attendance->id,
                'start_time' => $tomorrow->format('Y-m-d') . ' 12:00:00',
                'end_time' => $tomorrow->format('Y-m-d') . ' 13:00:00',
            ]);

            $admin = User::factory()->create(['is_admin' => 1])->first();

            $this->actingAs($admin)
            ->get('/admin/attendance/list')
            ->assertStatus(200);

            $this->get('/admin/attendance/list?date=' . $tomorrow)
            ->assertStatus(200)
            ->assertSee($tomorrow->format('Y年m月d日'))
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('01:00')
            ->assertSee('08:00');



    }


    







}
