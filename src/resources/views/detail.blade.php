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
                    <input type="date" name="date" value="{{ \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') }}" class="form-control">
                </div>
            </div>

            <!-- 出勤・退勤 -->
            <div class="detail-row">
                <div class="detail-label">出勤・退勤</div>
                <div class="detail-value">
                    <input type="time" name="clock_in" value="{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}" class="form-control">
                    <span class="time-separator">~</span>
                    <input type="time" name="clock_out" value="{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}" class="form-control">
                </div>
                @error('clock_in')
                    <span class="error">{{ $message }}</span>
                @enderror
                @error('clock_out')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- 休憩 -->
            <div class="detail-row">
                <div class="detail-label">休憩</div>
                <div class="detail-value">
                    @forelse($attendance->breaks as $break)
                        <div class="break">
                            <input type="time" name="break_start_{{ $break->id }}" value="{{ $break->start_time ? \Carbon\Carbon::parse($break->start_time)->format('H:i') : '' }}" class="form-control">
                            <span class="time-separator">~</span>
                            <input type="time" name="break_end_{{ $break->id }}" value="{{ $break->end_time ? \Carbon\Carbon::parse($break->end_time)->format('H:i') : '' }}" class="form-control">
                        </div>
                    @empty
                        <div class="break">
                            <input type="time" name="break_start_new" class="form-control">
                            <span class="time-separator">~</span>
                            <input type="time" name="break_end_new" class="form-control">
                        </div>
                    @endforelse
                </div>
                @error('start_time')
                    <span class="error">{{ $message }}</span>
                @enderror
                @error('end_time')
                    <span class="error">{{ $message }}</span>
                @enderror
                
            </div>

            <!-- 備考 -->
            <div class="detail-row">
                <div class="detail-label">備考</div>
                <div class="detail-value">
                    <textarea name="reason" class="form-control">{{ $attendance->modificationRequest ? $attendance->modificationRequest->reason : '' }}</textarea>
                </div>
                @error('reason')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="action-buttons">
            <button type="submit" class="edit-button">修正</button>
        </div>
    </form>
</div>
@endsection
