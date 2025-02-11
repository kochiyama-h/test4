@extends('layouts.app')

@section('title', '勤怠一覧 - COACHTECH')

@section('additional_css')
    <link href="{{ asset('css/list.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="attendance-list-container">
    <h2 class="page-title">勤怠一覧</h2>

    <div class="month-selector">
        <a href="{{ route('attendance.list', ['month' => $previousMonth]) }}" class="month-nav">
            ← 前月
        </a>
        <div class="current-month">{{ \Carbon\Carbon::parse($currentMonth)->format('Y/m') }}</div>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="month-nav">
            翌月 →
        </a>
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
                    <td>{{ $attendance->date }}</td>
                    <td>{{ $attendance->clock_in }}</td> <!-- 出勤時間 (clock_in) -->
                    <td>{{ $attendance->clock_out }}</td> <!-- 退勤時間 (clock_out) -->
                    <td>{{ sprintf('%02d:%02d', floor($attendance->break_time / 60), $attendance->break_time % 60) }}</td> <!-- 休憩時間 (break_time) -->
                    <td>{{ $attendance->total_time_formatted }}</td> <!-- 合計時間 (total_time_formatted) -->
                    <td>
                        <a href="{{ route('attendance.detail', ['id' => $attendance->id]) }}" class="detail-link">
                            詳細
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection