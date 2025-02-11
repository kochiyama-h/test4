@extends('layouts.app')

@section('title', '勤怠一覧')

@section('additional_css')
<link rel="stylesheet" href="{{ asset('css/admin_list.css') }}">
@endsection

@section('content')
<div class="attendance-list-container">
    <h1 class="page-title">{{ $date->format('Y') }}年{{ $date->format('m') }}月{{ $date->format('d') }}日の勤怠一覧</h1>

    <div class="date-navigation">
        <a href="{{ route('admin.attendance.list', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" class="nav-button prev-day">
            ← 前日
        </a>
        <div class="current-date">{{ $date->format('Y/m/d') }}</div>
        <a href="{{ route('admin.attendance.list', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" class="nav-button next-day">
            翌日 →
        </a>
    </div>

    <div class="attendance-table-wrapper">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
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
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->clock_in_time }}</td>
                    <td>{{ $attendance->clock_out_time }}</td>
                    <td>{{ $attendance->break_time }}</td>
                    <td>{{ $attendance->total_time }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection