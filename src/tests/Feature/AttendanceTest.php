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

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    

    //テストID６:出勤機能
     /**
     * 出勤ボタンが正しく機能する
     */

     public function test_attendance_button_is_displayed_for_user_not_started_working()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        Auth::login($user);

        // ステータスが「勤務外」の場合、出勤ボタンが表示されることを確認
        $response = $this->get(route('attendance.index'));
        $response->assertSee('出勤');

        // 出勤処理を行う
        $response = $this->post(route('attendance.start'));

        // 勤務中のステータスが表示されることを確認
        $response->assertRedirect(route('attendance.index'));
        $this->get(route('attendance.index'))->assertSee('出勤中');
    }

    
    

    /**
     * 出勤は一日一回のみできる
     */
    public function test_user_cannot_clock_in_twice_in_one_day()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        Auth::login($user);

        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working',
            'clock_in' => now(),
        ]);

        
        $attendance->update([
            'status' => 'left',
            'clock_out' => now(),
        ]);

        $this->get('/attendance');  

        // 出勤ボタンが表示されないことを確認
        $this->get(route('attendance.index'))->assertDontSee('出勤');
    }


    /**
     * 出勤時刻が管理画面で確認できる
     */

    public function test_clock_in_time_is_recorded_in_attendance_record()
    {
        // テスト用の固定時刻を設定
    $fixedTime = Carbon::create(2024, 2, 1, 9, 0, 0); // 2024年2月1日 9:00
    Carbon::setTestNow($fixedTime);

    // ユーザーを作成してログイン
    $user = User::factory()->create();
    Auth::login($user);

    // 出勤処理を実行
    $response = $this->post(route('attendance.start'));
    $response->assertRedirect(route('attendance.index'));

    // 勤怠記録が正しく作成されたことを確認
    $attendance = AttendanceRecord::where('user_id', $user->id)
        ->where('date', $fixedTime->toDateString())
        ->first();

    $this->assertNotNull($attendance); // 勤怠記録が作成されていることを確認
    $this->assertEquals('working', $attendance->status); // ステータスが「出勤中」になっていることを確認
    
    // 出勤時刻の確認（文字列として比較）
    $this->assertEquals(
        $fixedTime->format('H:i'),
        Carbon::parse($attendance->clock_in)->format('H:i')
    );

    // 勤怠一覧画面で確認
    $response = $this->get(route('attendance.list'));
    $response->assertStatus(200);
    $response->assertSee($fixedTime->format('H:i'));
    
    // テスト用の時刻をリセット
    Carbon::setTestNow();
    }


    //テストID7:休憩機能
     /**
     * 休憩ボタンが正しく機能する
     */
    public function test_attendance_status_changes_to_breaking_after_clock_in()
    {
        
        $user = User::factory()->create();
        Auth::login($user);

        AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working',
            'clock_in' => now(),
        ]);
        
        
        $this->get(route('attendance.index'))->assertSee('休憩入');
        $response = $this->post(route('attendance.break.start'));
        $response->assertRedirect(route('attendance.index'));
        $this->get(route('attendance.index'))->assertSee('休憩中');

    }


     /**
     * 休憩は一日に何回でもできる
     */
     public function test_user_can_break_manytimes()
     {
        $user = User::factory()->create();
        Auth::login($user);
    
        AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working',
            'clock_in' => now(),
        ]);

        $response = $this->post(route('attendance.break.start'));
        $response = $this->post(route('attendance.break.end'));
        
        $this->get(route('attendance.index'))->assertSee('休憩入');

     }




     /**
     * 出勤時刻が管理画面で確認できる休憩戻ボタンが正しく機能する
     */
     public function test_user_can_breakend_button()
     {
        $user = User::factory()->create();
        Auth::login($user);
    
        AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working',
            'clock_in' => now(),
        ]);

        $response = $this->post(route('attendance.break.start')); 
        $response = $this->post(route('attendance.break.end'));
        
        $this->get(route('attendance.index'))->assertSee('休憩入');
        $this->get(route('attendance.index'))->assertSee('出勤中');


     }
    



     /**
     * 休憩戻は一日に何回でもできる
     */
    public function test_user_can_breakend_manytimes()
     {
        $user = User::factory()->create();
        Auth::login($user);
    
        AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working',
            'clock_in' => now(),
        ]);

        $response = $this->post(route('attendance.break.start'));
        $response = $this->post(route('attendance.break.end'));
        $response = $this->post(route('attendance.break.start'));
        
        $this->get(route('attendance.index'))->assertSee('休憩戻');

     }



     /**
     * 休憩時刻が管理画面で確認できる
     */

     public function test_break_time_is_recorded_in_attendance_record()
{
    // テスト用の固定時刻を設定
    $fixedTime = Carbon::create(2024, 2, 1, 9, 0, 0);
    Carbon::setTestNow($fixedTime);

    // ユーザーを作成してログイン
    $user = User::factory()->create();
    Auth::login($user);

    // 出勤処理
    $this->post(route('attendance.start'));

    // 休憩開始
    Carbon::setTestNow($fixedTime->copy()->addHours(2)); // 11:00
    $this->post(route('attendance.break.start'));

    // 休憩終了
    Carbon::setTestNow($fixedTime->copy()->addHours(3)); // 12:00
    $this->post(route('attendance.break.end'));

    // 勤怠記録を取得
    $attendance = AttendanceRecord::where('user_id', $user->id)
        ->where('date', $fixedTime->toDateString())
        ->first();

    $this->assertNotNull($attendance);
    $this->assertEquals('working', $attendance->status);

    // 休憩時間の確認
    $breakTime = $attendance->breaks->first();
    $this->assertNotNull($breakTime);
    $this->assertEquals('11:00', Carbon::parse($breakTime->start_time)->format('H:i'));
    $this->assertEquals('12:00', Carbon::parse($breakTime->end_time)->format('H:i'));

    // 勤怠一覧画面で確認
    $response = $this->get(route('attendance.list'));
    $response->assertStatus(200);
    $response->assertSee('01:00'); // 休憩時間が1時間であることを確認

    Carbon::setTestNow();
}

    //テストID8:退勤機能
     /**
     * 退勤ボタンが正しく機能する
     */
    public function test_attendance_button_is_displayed_for_user_not_finished_working()
    {
        
        $user = User::factory()->create();
        Auth::login($user);

        AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working',
            'clock_in' => now(),
        ]);
        
        $this->get(route('attendance.index'))->assertSee('退勤');
        

        // 退勤処理を行う
        $response = $this->post(route('attendance.finish'));

        // 退勤済のステータスが表示されることを確認
        $response->assertRedirect(route('attendance.index'));
        $this->get(route('attendance.index'))->assertSee('退勤済');

    }

    /**
     * 退勤時刻が管理画面で確認できる
     */
    public function test_clock_out_time_is_recorded_in_attendance_record()
{
    // テスト用の固定時刻を設定
    $fixedTime = Carbon::create(2024, 2, 1, 9, 0, 0);
    Carbon::setTestNow($fixedTime);

    // ユーザーを作成してログイン
    $user = User::factory()->create();
    Auth::login($user);

    // 出勤処理
    $this->post(route('attendance.start'));

    // 退勤処理
    Carbon::setTestNow($fixedTime->copy()->addHours(8)); // 17:00
    $this->post(route('attendance.finish'));

    // 勤怠記録を取得
    $attendance = AttendanceRecord::where('user_id', $user->id)
        ->where('date', $fixedTime->toDateString())
        ->first();

    $this->assertNotNull($attendance);
    $this->assertEquals('left', $attendance->status);

    // 退勤時刻の確認
    $this->assertEquals('17:00', Carbon::parse($attendance->clock_out)->format('H:i'));

    // 勤怠一覧画面で確認
    $response = $this->get(route('attendance.list'));
    $response->assertStatus(200);
    $response->assertSee('17:00'); // 退勤時刻が表示されていることを確認
    $response->assertSee('08:00'); // 勤務時間が8時間であることを確認

    Carbon::setTestNow();
}
    


}





