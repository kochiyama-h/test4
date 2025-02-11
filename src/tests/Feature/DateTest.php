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

    //テストID4:日時取得機能
     /**
     * 現在の日時情報がUIと同じ形式で出力されている
     */

     public function test_attendance_screen_displays_current_datetime()
    {
        
        $user = User::factory()->create();
        Auth::login($user);

       
        $response = $this->get('/attendance');

        // 現在の日付と時間を取得（UIが期待する形式に合わせる）
        $currentDate = now()->format('Y年m月d日') . " (" . ['日', '月', '火', '水', '木', '金', '土'][now()->dayOfWeek] . ")";
        $currentTime = now()->format('H:i');  // 'H:i' の形式で時間を取得

        // ページに表示されている日時情報が現在の日付と一致することを確認
        $response->assertSee($currentDate);  // 現在の日付がUIに表示されているかを確認
        $response->assertSee($currentTime);  // 現在の時刻がUIに表示されているかを確認
    }

   
    


}





