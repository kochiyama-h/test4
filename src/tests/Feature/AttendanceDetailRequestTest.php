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

class AttendanceDetailRequestTest extends TestCase
{
    use RefreshDatabase;

    //ID11:勤怠詳細情報修正機能（一般ユーザー）
    /**
     *  出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */     
    public function test_attendance_request_clockin_clockout()
    {
        $user = User::factory()->create();

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

            $response = $this->get(route('attendance.detail', ['id' => $attendance->id]));
            $response->assertStatus(200);

            // 出勤時間を退勤時間より後に設定
            $invalidClockIn = '19:00'; // 退勤時間 18:00 より後
            $validClockOut = '18:00';

            // 修正リクエストを送信
            $response = $this->put(route('attendance.update', $attendance->id), [
                'date' => $currentDate->format('Y-m-d'),
                'clock_in' => $invalidClockIn,
                'clock_out' => $validClockOut,
            ]);

            $response->assertSessionHasErrors(['clock_out' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    /**
     *  休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */ 

     /**
     *  休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */ 

     /**
     *  備考欄が未入力の場合のエラーメッセージが表示される
     */ 
            public function test_reason_null ()
            {

                $user = User::factory()->create();
    
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
    
                $response = $this->get(route('attendance.detail', ['id' => $attendance->id]));
                $response->assertStatus(200);
                // 備考欄を未入力のまま更新処理を実行
               $updateResponse = $this->put(route('attendance.update', ['attendance' => $attendance->id]), [
                   'date' => $currentDate->format('Y-m-d'),
                   'clock_in' => '09:00',
                   'clock_out' => '18:00',
                   'reason' => '', // 備考欄を未入力
               ]);
   
               // バリデーションエラーの確認
               $updateResponse->assertSessionHasErrors(['reason' => '備考を記入してください']);
            }


        }