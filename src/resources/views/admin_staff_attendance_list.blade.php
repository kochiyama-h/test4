@extends('layouts.app')

@section('title', '勤怠一覧 - COACHTECH')

@section('additional_css')
    <link href="{{ asset('css/admin_staff_attendance_list.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="staff-list-container">
    <h1 class="staff-name-title">{{ $staff->name }}さんの勤怠</h1>
    
    <div class="month-navigator">
        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $prevMonth]) }}" class="month-nav-btn prev">← 前月</a>
        <div class="current-month">{{ $month }}</div>
        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $nextMonth]) }}" class="month-nav-btn next">翌月 →</a>
    </div>

    <div class="attendance-table-wrapper">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                <tr>
                    <td>{{ date('m/d', strtotime($attendance->date)) }} ({{ ['日','月','火','水','木','金','土'][date('w', strtotime($attendance->date))] }})</td>
                    <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->formattedBreakTime ?? '-' }}</td>
                    <td>{{ $attendance->formattedTotalTime ?? '-' }}</td>
                    <td><a href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}
                    " class="detail-link">
                            詳細
                        </a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="csv-export">
        <button class="csv-btn">CSV出力</button>
    </div>
</div>
@endsection
