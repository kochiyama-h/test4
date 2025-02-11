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

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;


    //勤怠詳細情報取得機能（一般ユーザー）

    public function test_attendance_detail_page()
    {
        $user = User::factory()->create([
            'name' => 'aaa', // 名前を "aaa" に設定
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

        Auth::login($user);
    
        
    
        
        // 詳細ページに遷移
        $response = $this->get(route('attendance.detail', ['id' => $attendance->id]));
        $response->assertStatus(200);
        
        
       
        /**
         *  勤怠詳細画面の「名前」がログインユーザーの氏名になっている
         */       
        $response->assertSee('aaa'); 


        /**
         *  勤怠詳細画面の「日付」が選択した日付になっている
         */ 
        $response->assertSee(Carbon::now()->format('Y-m-d'));


         /**
         *   「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
         */
        $response->assertSee('09:00');
        $response->assertSee('18:00'); 

        /**
         *  「休憩」にて記されている時間がログインユーザーの打刻と一致している
         */ 
        $response->assertSee('12:00');
        $response->assertSee('13:00');
        
    }
}
