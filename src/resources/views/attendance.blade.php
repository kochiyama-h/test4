@extends('layouts.app')

@section('title', '勤怠管理 - COACHTECH')

@section('additional_css')
    <link href="{{ asset('css/attendance.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="attendance-container">
    <div class="attendance-content">
        <div class="status-badge {{ $status }}">
            @switch($status)
                @case('working')
                    出勤中
                    @break
                @case('break')
                    休憩中
                    @break
                @case('left')
                    退勤済
                    @break
                @default
                    勤務外
            @endswitch
        </div>

        <div class="date">{{ $date }}</div>
        <div class="time">{{ $time }}</div>

        <div class="button-group">
            @switch($status)
                @case('working')
                    <form method="POST" action="{{ route('attendance.finish') }}" class="attendance-form">
                        @csrf
                        <button type="submit" class="button button-black">退勤</button>
                    </form>
                    <form method="POST" action="{{ route('attendance.break.start') }}" class="attendance-form">
                        @csrf
                        <button type="submit" class="button button-white">休憩入</button>
                    </form>
                    @break

                @case('break')
                    <form method="POST" action="{{ route('attendance.break.end') }}" class="attendance-form">
                        @csrf
                        <button type="submit" class="button button-white">休憩戻</button>
                    </form>
                    @break

                @case('left')
                    <p class="completion-message">お疲れ様でした。</p>
                    @break

                @default
                    <form method="POST" action="{{ route('attendance.start') }}" class="attendance-form">
                        @csrf
                        <button type="submit" class="button button-black">出勤</button>
                    </form>
            @endswitch
        </div>
    </div>
</div>
@endsection


