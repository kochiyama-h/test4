<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminDetailRequest;
use Illuminate\Http\Request;
use App\Models\ModificationRequest;
use App\Models\AttendanceRecord;
use App\Models\BreakTime;
use App\Http\Requests\DetailRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    //勤怠修正
    public function update(DetailRequest $request, AttendanceRecord $attendance) 
{
    // 修正リクエストのデータを初期化
    $modificationData = [
        'user_id' => auth()->id(),
        'attendance_record_id' => $attendance->id,
        'before_date' => $attendance->date,
        'after_date' => $request->input('date'),
        'reason' => $request->input('reason', ''),
        'status' => 'pending',
    ];

    // 出勤・退勤時間の処理
    if ($request->has('clock_in')) {
        $modificationData['before_clock_in'] = $attendance->clock_in;
        $modificationData['after_clock_in'] = \Carbon\Carbon::parse($request->input('date') . ' ' . $request->input('clock_in'));
    }

    if ($request->has('clock_out')) {
        $modificationData['before_clock_out'] = $attendance->clock_out;
        $modificationData['after_clock_out'] = \Carbon\Carbon::parse($request->input('date') . ' ' . $request->input('clock_out'));
    }

    // 休憩時間の処理を修正
    $breakModifications = [];
    foreach ($attendance->breaks as $break) {
        $breakStartKey = "break_start_{$break->id}";
        $breakEndKey = "break_end_{$break->id}";

        // 休憩開始時間と終了時間の両方が入力されている場合のみ処理
        if ($request->filled($breakStartKey) && $request->filled($breakEndKey)) {
            $breakModifications[] = [
                'before_break_start' => $break->start_time,
                'after_break_start' => \Carbon\Carbon::parse($request->input('date') . ' ' . $request->input($breakStartKey)),
                'before_break_end' => $break->end_time,
                'after_break_end' => \Carbon\Carbon::parse($request->input('date') . ' ' . $request->input($breakEndKey))
            ];
        }
    }

    // 新規の休憩時間の処理
    if ($request->filled('break_start_new') && $request->filled('break_end_new')) {
        $breakModifications[] = [
            'before_break_start' => null,
            'after_break_start' => \Carbon\Carbon::parse($request->input('date') . ' ' . $request->input('break_start_new')),
            'before_break_end' => null,
            'after_break_end' => \Carbon\Carbon::parse($request->input('date') . ' ' . $request->input('break_end_new'))
        ];
    }

    // 休憩時間の修正データをメインの修正データに統合
    if (!empty($breakModifications)) {
        // 最初の休憩時間のデータを使用
        $modificationData = array_merge($modificationData, $breakModifications[0]);
    }

    // 修正リクエストを作成
    ModificationRequest::create($modificationData);

    // 処理完了後、'pending' ビューを返す
    return view('pending', compact('attendance'));
}



    //申請一覧画面
    public function request()
{
    $userId = auth()->id();

    // 承認待ちと承認済みの申請を取得
    $pendingRequests = ModificationRequest::with('user', 'attendanceRecord')
        ->where('status', 'pending')
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();

    $approvedRequests = ModificationRequest::with('user', 'attendanceRecord')
        ->where('status', 'approved')
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();

    // ビューにデータを渡して表示
    return view('request', compact('pendingRequests', 'approvedRequests'));
}


    //管理者用勤怠修正
    public function recordChange(AdminDetailRequest $request, AttendanceRecord $attendance)
    {
        // 日付部分を付加して出勤・退勤時刻を更新
        $clockIn = $request->input('clock_in') ? Carbon::parse($request->input('date').' '.$request->input('clock_in'))->format('Y-m-d H:i:s') : null;
        $clockOut = $request->input('clock_out') ? Carbon::parse($request->input('date').' '.$request->input('clock_out'))->format('Y-m-d H:i:s') : null;

        // 勤怠レコードの更新
        $attendance->update([
            'date' => $request->input('date'),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'reason' => $request->input('reason') ?? null,
        ]);

        // 休憩時間の更新（休憩がある場合）
        foreach ($attendance->breaks as $break) {
            $breakStart = $request->input("break_start_{$break->id}");
            $breakEnd = $request->input("break_end_{$break->id}");

            if ($breakStart && $breakEnd) {
                // 休憩の開始・終了時刻に日付を追加して更新
                $break->update([
                    'start_time' => Carbon::parse($request->input('date').' '.$breakStart),
                    'end_time' => Carbon::parse($request->input('date').' '.$breakEnd),
                ]);
            }
        }

        // 新しい休憩時間の追加（新規の場合）
        if ($request->has('break_start_new') && $request->has('break_end_new')) {
            $break = new BreakTime();
            $break->attendance_record_id = $attendance->id;
            $break->start_time = Carbon::parse($request->input('date').' '.$request->input('break_start_new'));
            $break->end_time = Carbon::parse($request->input('date').' '.$request->input('break_end_new'));
            $break->save();
        }

        

        

        return redirect()->route('admin.attendance.list', $attendance->id);

        

        
    }


    //管理者用申請一覧画面
    public function adminRequest()
    {
        // 承認待ちと承認済みのリクエストを取得
        $requests = ModificationRequest::with('user')->get();
        
        return view('admin_request', compact('requests'));
    }

    //管理者用申請承認画面
    public function adminApprove(Request $request)
    {
        // IDをリクエストパラメータから取得（例: /stamp_correction_request/approve?id=1）
        $modificationRequest = ModificationRequest::with('user')->find($request->id);

        return view('admin_request_approve', compact('modificationRequest'));
    }

    //管理者用申請詳細画面
    public function adminApproveDetail($modificationRequestId)
    {
        // ModificationRequest を取得
        $modificationRequest = ModificationRequest::findOrFail($modificationRequestId);

        // ステータスが 'pending' の場合のみ承認処理を行う
        if ($modificationRequest->status === 'pending') {
            // 承認済みに変更
            $modificationRequest->status = 'approved';
            $modificationRequest->save();

            // 対応する AttendanceRecord のデータを更新
            $attendanceRecord = AttendanceRecord::where('user_id', $modificationRequest->user_id)
                ->whereDate('clock_in', $modificationRequest->after_date)
                ->first();
            
            if ($attendanceRecord) {
                $attendanceRecord->clock_in = $modificationRequest->after_clock_in;
                $attendanceRecord->clock_out = $modificationRequest->after_clock_out;
                $attendanceRecord->reason = $modificationRequest->reason;
                $attendanceRecord->save();
            }

            // 休憩情報を更新
            $break = BreakTime::where('attendance_record_id', $attendanceRecord->id)->first();
            if ($break) {
                $break->start_time = $modificationRequest->after_break_start;
                $break->end_time = $modificationRequest->after_break_end;
                $break->save();
            }

            // 休憩2の情報がある場合
            if ($modificationRequest->after_break_start2 && $modificationRequest->after_break_end2) {
                $break2 = BreakTime::where('attendance_record_id', $attendanceRecord->id)->where('is_second', true)->first();
                if ($break2) {
                    $break2->start_time = $modificationRequest->after_break_start2;
                    $break2->end_time = $modificationRequest->after_break_end2;
                    $break2->save();
                }
            }
        }

        return redirect()->route('admin.attendance.list');
    }

    


    

}

    
