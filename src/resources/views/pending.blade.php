@extends('layouts.app')

@section('title', '勤怠詳細 - COACHTECH')

@section('additional_css')
    <link href="{{ asset('css/detail.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="detail-content">
            <!-- 名前 -->
            <div class="detail-row">
                <div class="detail-label">名前</div>
                <div class="detail-value">{{ $attendance->user->name }}</div>
            </div>

            <!-- 日付 -->
            <div class="detail-row">
                <div class="detail-label">日付</div>
                <div class="detail-value">
                    <input type="date" name="date" value="{{ $attendance->modificationRequest->after_date ?? \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') }}" class="form-control" disabled>
                </div>
            </div>

            <!-- 出勤・退勤 -->
            <div class="detail-row">
                <div class="detail-label">出勤・退勤</div>
                <div class="detail-value">
                    <input type="time" name="clock_in" value="{{ $attendance->modificationRequest ? \Carbon\Carbon::parse($attendance->modificationRequest->after_clock_in)->format('H:i') : ($attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}" class="form-control" disabled>
                    <span class="time-separator">~</span>
                    <input type="time" name="clock_out" value="{{ $attendance->modificationRequest ? \Carbon\Carbon::parse($attendance->modificationRequest->after_clock_out)->format('H:i') : ($attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}" class="form-control" disabled>
                </div>
            </div>

            <!-- 休憩 -->
            <div class="detail-row">
                <div class="detail-label">休憩</div>
                <div class="detail-value">
                    @if($attendance->breaks->isNotEmpty())
                        @foreach($attendance->breaks as $index => $break)
                            <div class="break">
                                <input type="time" name="breaks[{{ $index }}][start_time]" 
                                    value="{{ $attendance->modificationRequest ? \Carbon\Carbon::parse($attendance->modificationRequest->after_break_start)->format('H:i') : ($break->start_time ? \Carbon\Carbon::parse($break->start_time)->format('H:i') : '') }}" 
                                    class="form-control" disabled>
                                <span class="time-separator">~</span>
                                <input type="time" name="breaks[{{ $index }}][end_time]" 
                                    value="{{ $attendance->modificationRequest ? \Carbon\Carbon::parse($attendance->modificationRequest->after_break_end)->format('H:i') : ($break->end_time ? \Carbon\Carbon::parse($break->end_time)->format('H:i') : '') }}" 
                                    class="form-control" disabled>
                            </div>
                        @endforeach
                    @else
                        なし
                    @endif                    
                </div>
            </div>

            <!-- 備考 -->
            <div class="detail-row">
                <div class="detail-label">備考</div>
                <div class="detail-value">
                    <textarea name="reason" class="form-control" disabled>{{ $attendance->modificationRequest ? $attendance->modificationRequest->reason : '' }}</textarea>
                </div>
            </div>

        <div class="action-buttons">
            <p class="error-message" style="color: red;">承認待ちのため修正はできません</p>
        </div>
    </form>
</div>
@endsection