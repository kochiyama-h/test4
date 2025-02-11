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






class AttendanceListTest extends TestCase
{
    use RefreshDatabase;
     //テストID9:勤怠一覧情報取得機能（一般ユーザー）
     /**
     * 自分が行った勤怠情報が全て表示されている
     */
        public function test_all_attendance_records_are_displayed_for_logged_in_user()
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

            
            $response = $this->get(route('attendance.list', ['month' => $currentDate->format('Y-m')]));
            $response->assertStatus(200);

            // 勤怠情報が表示されていることを確認
            $response->assertSee($currentDate->format('m/d'));
            $response->assertSee('09:00'); 
            $response->assertSee('18:00'); 
            $response->assertSee('01:00');
            $response->assertSee('08:00');
        }

    /**
     * 勤怠一覧画面に遷移した際に現在の月が表示される
     */
        public function test_current_month_attendance_list()
        {
            $user = User::factory()->create();
            $currentMonth = Carbon::now()->format('Y/m');

            Auth::login($user);

            $response = $this->get(route('attendance.list', ['month' => $currentMonth]));        
            $response->assertSee($currentMonth);
        }

    /**
     * 「前月」を押下した時に表示月の前月の情報が表示される
     */
    public function test_previous_month_attendance_records_are_displayed()
    {
        $user = User::factory()->create();
    
        // 先月の日付を設定（2025年1月5日）
        $previousMonthDate = Carbon::now()->subMonth()->startOfMonth()->addDays(4); // 1月5日
    
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
    
        // ユーザーとしてログイン
        Auth::login($user);
    
        // 現在の月の勤怠一覧ページにアクセス
        $currentMonth = Carbon::now()->format('Y-m');
        $previousMonth = $previousMonthDate->format('Y-m'); // 2025-01
    
        $response = $this->get(route('attendance.list', ['month' => $currentMonth]));
        $response->assertStatus(200);
    
        // 「前月」ボタンを押す（URLへリダイレクト）
        $response = $this->get(route('attendance.list', ['month' => $previousMonth]));
        $response->assertStatus(200);
    
        // 勤怠情報が表示されていることを確認
        $response->assertSee($previousMonthDate->format('m/d')); // "01/05" が表示されているか
        $response->assertSee('09:00'); 
        $response->assertSee('18:00'); 
        $response->assertSee('01:00'); // 休憩時間
        $response->assertSee('08:00'); // 労働時間（例: 9:00 - 18:00 から休憩1時間を引く）

    }

        
    /**
    * 「翌月」を押下した時に表示月の前月の情報が表示される
    */
    public function test_next_month_attendance_records_are_displayed()
    {
        $user = User::factory()->create();

        // 翌月の日付を設定（2025年3月5日）
        $nextMonthDate = Carbon::now()->addMonth()->startOfMonth()->addDays(4); // 3月5日

        // テスト用の勤怠データを作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => $nextMonthDate->format('Y-m-d'),
            'clock_in' => $nextMonthDate->format('Y-m-d') . ' 09:00:00',
            'clock_out' => $nextMonthDate->format('Y-m-d') . ' 18:00:00',
            'status' => 'left',
        ]);

        // 休憩時間を追加
        BreakTime::create([
            'attendance_record_id' => $attendance->id,
            'start_time' => $nextMonthDate->format('Y-m-d') . ' 12:00:00',
            'end_time' => $nextMonthDate->format('Y-m-d') . ' 13:00:00',
        ]);

        // ユーザーとしてログイン
        Auth::login($user);

        // 現在の月の勤怠一覧ページにアクセス
        $currentMonth = Carbon::now()->format('Y-m');
        $nextMonth = $nextMonthDate->format('Y-m'); // 2025-03

        $response = $this->get(route('attendance.list', ['month' => $currentMonth]));
        $response->assertStatus(200);

        // 「翌月」ボタンを押す（URLへリダイレクト）
        $response = $this->get(route('attendance.list', ['month' => $nextMonth]));
        $response->assertStatus(200);

        // 勤怠情報が表示されていることを確認
        $response->assertSee($nextMonthDate->format('m/d')); // "03/05" が表示されているか
        $response->assertSee('09:00'); 
        $response->assertSee('18:00'); 
        $response->assertSee('01:00'); // 休憩時間
        $response->assertSee('08:00'); // 労働時間（例: 9:00 - 18:00 から休憩1時間を引く）
    }

    /**
    * 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    */
    public function test_attendance_detail_page_is_accessible()
    {
        $user = User::factory()->create();
    
        // 勤怠情報を登録
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'clock_in' => Carbon::now()->format('Y-m-d') . ' 09:00:00',
            'clock_out' => Carbon::now()->format('Y-m-d') . ' 18:00:00',
            'status' => 'left',
        ]);
    
        Auth::login($user);
    
        // 勤怠一覧ページを表示
        $response = $this->get(route('attendance.list', ['month' => Carbon::now()->format('Y-m')]));
        $response->assertStatus(200);
    
        // 詳細ページへのリンクが表示されていることを確認
        $response->assertSee(route('attendance.detail', ['id' => $attendance->id]));
    
        // 詳細ページに遷移
        $response = $this->get(route('attendance.detail', ['id' => $attendance->id]));
        $response->assertStatus(200);
        $response->assertSee(Carbon::now()->format('Y-m-d'));
        
    }

}