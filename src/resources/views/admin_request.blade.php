@extends('layouts.app')

@section('title', '申請一覧')

@section('additional_css')
<link rel="stylesheet" href="{{ asset('css/admin_request.css') }}">
@endsection

@section('content')
@php
    $status = request()->query('status', 'pending'); // デフォルトは 'pending'
    $filteredRequests = $requests->filter(fn($request) => $request->status === $status);
@endphp

<div class="request-list-container">
    <h2 class="list-title">申請一覧</h2>

    <div class="tab-container">
        <a href="?status=pending" class="tab-item {{ $status === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="?status=approved" class="tab-item {{ $status === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <div class="request-table">
        <div class="table-header">
            <div class="header-cell status">状態</div>
            <div class="header-cell name">名前</div>
            <div class="header-cell target-date">対象日時</div>
            <div class="header-cell reason">申請理由</div>
            <div class="header-cell request-date">申請日時</div>
            <div class="header-cell details">詳細</div>
        </div>

        @foreach($filteredRequests as $request)
        <div class="table-row">
            <div class="cell status">
                <span class="status-badge {{ $request->status === 'pending' ? 'pending' : 'approved' }}">
                    {{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}
                </span>
            </div>
            <div class="cell name">{{ $request->user->name }}</div>
            <div class="cell target-date">{{ \Carbon\Carbon::parse($request->before_date)->format('Y/m/d') }}</div>
            <div class="cell reason">{{ $request->reason }}</div>
            <div class="cell request-date">{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</div>
            <div class="cell details">
                <a href="{{ route('admin.approve', ['id' => $request->id])  }}" class="detail-link">詳細</a>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
