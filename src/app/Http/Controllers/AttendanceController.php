<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\BreakTime;
use App\Models\User;
use App\Models\ModificationRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class AttendanceController extends Controller
{

  /**
     * 勤怠管理画面を表示
     */
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $attendance = AttendanceRecord::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        $status = $attendance ? $attendance->status : 'not_started';
        $dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'][$today->dayOfWeek];
        $date = $today->format("Y年m月d日") . " ({$dayOfWeek})";
        $time = Carbon::now()->format('H:i');

        return view('attendance', [
            'status' => $status,
            'date' => $date,
            'time' => $time,
        ]);
    }

    /**
     * 出勤開始処理
     */
    public function start(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $attendance = AttendanceRecord::updateOrCreate(
            ['user_id' => $userId, 'date' => $today],
            ['clock_in' => Carbon::now(), 'status' => 'working']
        );

        return redirect()->route('attendance.index');
    }

    /**
     * 退勤処理
     */
    public function finish(Request $request)
{
    $userId = Auth::id();
    $today = Carbon::today();

    $attendance = AttendanceRecord::where('user_id', $userId)
        ->where('date', $today)
        ->first();

    if ($attendance) {
        $attendance->update([
            'clock_out' => Carbon::now(),
            'status' => 'left', 
        ]);
    }

    return redirect()->route('attendance.index');
}

    /**
     * 休憩開始処理
     */
    public function breakStart(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $attendance = AttendanceRecord::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->status === 'working') {
            BreakTime::create([
                'attendance_record_id' => $attendance->id,
                'start_time' => Carbon::now(),
            ]);

            $attendance->update(['status' => 'break']);
        }

        return redirect()->route('attendance.index');
    }

    /**
     * 休憩終了処理
     */
    public function breakEnd(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $attendance = AttendanceRecord::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->status === 'break') {
            $break = BreakTime::where('attendance_record_id', $attendance->id)
                ->whereNull('end_time')
                ->latest()
                ->first();

            if ($break) {
                $break->update(['end_time' => Carbon::now()]);
                $attendance->update(['status' => 'working']);
            }
        }

        return redirect()->route('attendance.index');
    }


    /**
     * 勤怠一覧画面を表示
     */
    public function list(Request $request)
    {
        $userId = Auth::id();

        // 月のパラメータを取得（デフォルトは今月）
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        // 月の初日と末日を取得
        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // 対象月の勤怠データを取得
        $attendances = AttendanceRecord::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        // 現在の月を取得してビューに渡す
        $currentMonth = $month;

        // 前月と翌月のリンク用に月を取得
        $previousMonth = $startOfMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $startOfMonth->copy()->addMonth()->format('Y-m');

        // 曜日を日本語に変換する配列
        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];

        // 勤怠データの時間をフォーマット
        $attendances->transform(function ($attendance) use ($weekDays) {
            $attendance->clock_in = Carbon::parse($attendance->clock_in)->format('H:i');
            $attendance->clock_out = Carbon::parse($attendance->clock_out)->format('H:i');
            
            // 休憩時間（開始時間と終了時間を計算）
            $breaks = $attendance->breaks;
            $attendance->break_time = $breaks->sum(function($break) {
                return Carbon::parse($break->start_time)->diffInMinutes(Carbon::parse($break->end_time));
            });

            // 合計時間
            $attendance->total_time = Carbon::parse($attendance->clock_in)->diffInMinutes(Carbon::parse($attendance->clock_out)) - $attendance->break_time;
            $attendance->total_time_formatted = sprintf('%02d:%02d', floor($attendance->total_time / 60), $attendance->total_time % 60);

            // 日付を「02/03（月）」の形式に変換
            $dateObj = Carbon::parse($attendance->date);
            $dayOfWeek = $weekDays[$dateObj->dayOfWeek];
            $attendance->date = $dateObj->format('m/d') . "（{$dayOfWeek}）";

            return $attendance;
        });

        return view('list', [
            'attendances' => $attendances,
            'currentMonth' => $currentMonth,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
        ]);
    }


    // 勤怠詳細ページの表示
    public function detail($id)
    {
        $attendance = AttendanceRecord::findOrFail($id);
    
        // 該当する modification_request を取得
        $modificationRequest = ModificationRequest::where('attendance_record_id', $id)
            ->latest()
            ->first(); // 最新のリクエストを取得

        return view('detail', compact('attendance', 'modificationRequest'));
    }


    //管理者用勤怠一覧画面
    public function adminList(Request $request)
{
    // 指定された日付を取得（デフォルトは今日）
    $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
    
    // 指定された日付の勤怠データを取得
    $attendances = AttendanceRecord::whereDate('clock_in', $date->toDateString())
        ->with(['user', 'breaks'])
        ->get()
        ->map(function ($attendance) {
            // 休憩時間の合計を計算
            $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                if (!$break->end_time) return 0;
                return Carbon::parse($break->end_time)->diffInMinutes(Carbon::parse($break->start_time));
            });

            // 勤務時間の計算
            $totalWorkMinutes = 0;
            if ($attendance->clock_out) {
                $totalWorkMinutes = Carbon::parse($attendance->clock_out)
                    ->diffInMinutes(Carbon::parse($attendance->clock_in)) - $totalBreakMinutes;
            }

            // 各時間を整形
            $clockInTime = Carbon::parse($attendance->clock_in)->format('H:i');
            $clockOutTime = $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '--:--';
            $breakTimeFormatted = sprintf('%02d:%02d', floor($totalBreakMinutes / 60), $totalBreakMinutes % 60);
            $totalTimeFormatted = sprintf('%02d:%02d', floor($totalWorkMinutes / 60), $totalWorkMinutes % 60);

            // オブジェクトとして返す（配列ではなく）
            $attendance->clock_in_time = $clockInTime;
            $attendance->clock_out_time = $clockOutTime;
            $attendance->break_time = $breakTimeFormatted;
            $attendance->total_time = $totalTimeFormatted;

            return $attendance;
        });

    return view('admin_list', compact('date', 'attendances'));
}

    //スタッフ一覧画面表示
    public function adminStaffList()
    {
        // スタッフの一覧を取得（全ユーザーを取得）
        $staffMembers = User::all();

        return view('admin_staff_list', compact('staffMembers'));
    }

    //スタッフ別勤怠一覧
    public function adminStaffAttendance($id, Request $request)
{
    $staff = User::findOrFail($id);
    
    // クエリパラメータで月指定、デフォルトは今月
    $month = $request->query('month', now()->format('Y/m'));
    
    // 検索用の日付を作成
    $baseDate = \Carbon\Carbon::createFromFormat('Y/m', $month)->startOfMonth();
    $startOfMonth = $baseDate->format('Y-m-d');
    $endOfMonth = $baseDate->copy()->endOfMonth()->format('Y-m-d');
    
    

    // 検索条件を修正
    $attendances = AttendanceRecord::where('user_id', $id)
        ->whereBetween('date', [$startOfMonth, $endOfMonth])
        ->orderBy('date')
        ->get();

    // 休憩時間と合計時間の計算
    foreach ($attendances as $attendance) {
        // 休憩時間の計算
        $breaks = BreakTime::where('attendance_record_id', $attendance->id)->get();
        $totalBreakTime = 0;
        
        foreach ($breaks as $break) {
            if ($break->start_time && $break->end_time) {  // null チェックを追加
                $startTime = \Carbon\Carbon::parse($break->start_time);
                $endTime = \Carbon\Carbon::parse($break->end_time);
                $totalBreakTime += $startTime->diffInMinutes($endTime);
            }
        }

        // 出勤時間と退勤時間の差を計算
        if ($attendance->clock_in && $attendance->clock_out) {  // null チェックを追加
            $clockIn = \Carbon\Carbon::parse($attendance->clock_in);
            $clockOut = \Carbon\Carbon::parse($attendance->clock_out);
            $totalWorkTime = $clockIn->diffInMinutes($clockOut) - $totalBreakTime;

            // 時間を00:00の形式にする
            $attendance->formattedBreakTime = sprintf('%02d:%02d', floor($totalBreakTime / 60), $totalBreakTime % 60);
            $attendance->formattedTotalTime = sprintf('%02d:%02d', floor($totalWorkTime / 60), $totalWorkTime % 60);
        } else {
            $attendance->formattedBreakTime = '-';
            $attendance->formattedTotalTime = '-';
        }
    }

    // 前月と翌月のリンク用
    $prevMonth = $baseDate->copy()->subMonth()->format('Y/m');
    $nextMonth = $baseDate->copy()->addMonth()->format('Y/m');

    return view('admin_staff_attendance_list', compact('staff', 'attendances', 'month', 'prevMonth', 'nextMonth'));
}
    //管理者用勤怠詳細画面
    public function adminDetail($id)
    {
        $attendance = AttendanceRecord::findOrFail($id);
    
        // 該当する modification_request を取得
        $modificationRequest = ModificationRequest::where('attendance_record_id', $id)
            ->latest()
            ->first(); // 最新のリクエストを取得

        // 詳細ページにデータを渡して表示
        return view('admin_detail',  compact('attendance', 'modificationRequest'));
    }
}