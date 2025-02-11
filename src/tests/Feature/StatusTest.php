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

 

    //テストID5:ステータス確認機能
     /**
     * 勤務外の場合、勤怠ステータスが正しく表示される
     */
    public function test_status_is_off_duty_when_not_started()
    {
        $user = User::factory()->create();
        Auth::login($user);

        
        $response = $this->get('/attendance');        
        $response->assertSee('勤務外');
    }

      /**
     * 勤務中の場合、勤怠ステータスが正しく表示される
     */
    public function test_status_is_working_when_clocked_in()
    {
        $user = User::factory()->create();
        Auth::login($user);

        // 勤怠記録を作成（勤務中）
        AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working',
            'clock_in' => now(),
        ]);

        $response = $this->get('/attendance');        
        $response->assertSee('出勤中');
    }

      /**
     * 休憩中の場合、勤怠ステータスが正しく表示される
     */

     public function test_status_is_on_break_when_on_break()
    {
        $user = User::factory()->create();
        Auth::login($user);

        
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working',
            'clock_in' => now(),
        ]);

        
        $attendance->update(['status' => 'break']);
        $response = $this->get('/attendance');

        
        $response->assertSee('休憩中');
    }


      /**
     * 退勤済の場合、勤怠ステータスが正しく表示される
     */
    public function test_status_is_left_when_clocked_out()
    {
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

        $response = $this->get('/attendance');        
        $response->assertSee('退勤済');
    }

    

}





