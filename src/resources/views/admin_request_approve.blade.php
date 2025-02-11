@extends('layouts.app')

@section('title', '勤怠詳細')

@section('additional_css')
<link rel="stylesheet" href="{{ asset('css/admin_request_approve.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <div class="attendance-detail-content">
        <h2 class="detail-title">勤怠詳細</h2>
        
        <div class="detail-table">
            <div class="detail-row">
                <div class="detail-label">名前</div>
                <div class="detail-value">{{ $modificationRequest->user->name }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">日付</div>
                <div class="detail-value">
                    {{ \Carbon\Carbon::parse($modificationRequest->after_date)->format('Y年n月j日') }}
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">出勤・退勤</div>
                <div class="detail-value">
                    {{ \Carbon\Carbon::parse($modificationRequest->after_clock_in)->format('H:i') }}
                    <span class="time-separator">~</span>
                    {{ \Carbon\Carbon::parse($modificationRequest->after_clock_out)->format('H:i') }}
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">休憩</div>
                <div class="detail-value">
                    {{ \Carbon\Carbon::parse($modificationRequest->after_break_start)->format('H:i') }}
                    <span class="time-separator">~</span>
                    {{ \Carbon\Carbon::parse($modificationRequest->after_break_end)->format('H:i') }}
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">休憩2</div>
                <div class="detail-value">
                    @if($modificationRequest->after_break_start2 && $modificationRequest->after_break_end2)
                        {{ \Carbon\Carbon::parse($modificationRequest->after_break_start2)->format('H:i') }}
                        <span class="time-separator">~</span>
                        {{ \Carbon\Carbon::parse($modificationRequest->after_break_end2)->format('H:i') }}
                    @endif
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">備考</div>
                <div class="detail-value">{{ $modificationRequest->reason }}</div>
            </div>
        </div>

        @if($modificationRequest->status === 'pending')
            <form action="{{ route('admin.approve.detail', $modificationRequest->id) }}" method="POST" class="approve-form">
                @csrf
                <button type="submit" class="approve-button">承認</button>
            </form>
        @else
            <div class="approved-status">承認済み</div>
        @endif
    </div>
</div>
@endsection
