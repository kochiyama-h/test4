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

class AdminAttendanceDetailTest extends TestCase
{
    //ID13:勤怠詳細情報取得・修正機能（管理者）
    /**
     *  勤怠詳細画面に表示されるデータが選択したものになっている
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
    $this->actingAs($admin);

    // 勤怠詳細ページへアクセス
    $response = $this->get(route('admin.attendance.detail', ['id' => $attendance->id]));

    $response->assertStatus(200)
    ->assertSee($user->name)
    ->assertSee($currentDate->format('Y-m-d')) 
    ->assertSee('09:00')
    ->assertSee('18:00')
    ->assertSee('12:00') 
    ->assertSee('13:00');

    
}


    /**
    * 備考欄が未入力の場合のエラーメッセージが表示される
    */

    public function test_reason_null()
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
    $this->actingAs($admin);

    // 勤怠詳細ページへアクセス
    $this->get(route('admin.attendance.detail', ['id' => $attendance->id]));

    $updateResponse = $this->put(route('admin.record.change', ['attendance' => $attendance->id]), [
        'date' => $currentDate->format('Y-m-d'),
        'clock_in' => '09:00',
        'clock_out' => '18:00',
        'reason' => '', // 備考欄を未入力
    ]);

    // バリデーションエラーの確認
    $updateResponse->assertSessionHasErrors(['reason' => '備考を記入してください']);

    
}
}
