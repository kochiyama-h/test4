@extends('layouts.app')

@section('title', '申請一覧 - COACHTECH')

@section('additional_css')
    <link href="{{ asset('css/request.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="request-container">
    <h2 class="page-title">申請一覧</h2>

    <div class="tab-container">
        <a href="?status=pending" class="tab-item {{ request()->query('status') === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="?status=approved" class="tab-item {{ request()->query('status') === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <div class="request-table-wrapper">
        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日付</th>
                    <th>申請内容</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @if(request()->query('status') === 'pending')
                    @foreach($pendingRequests as $request)
                    <tr>
                        <td>
                            <span class="status-badge pending">
                                承認待ち
                            </span>
                        </td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->after_date)->format('Y/m/d') }}</td>
                        <td>{{ $request->reason }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('attendance.detail', $request->id) }}" class="detail-link">
                                詳細
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @elseif(request()->query('status') === 'approved')
                    @foreach($approvedRequests as $request)
                    <tr>
                        <td>
                            <span class="status-badge approved">
                                承認済み
                            </span>
                        </td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->after_date)->format('Y/m/d') }}</td>
                        <td>{{ $request->reason }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td>
                        <a href="{{ route('attendance.detail', $request->attendance_record_id) }}" class="detail-link">
                            詳細
                        </a>
                        </td>
                    </tr>
                    @endforeach
                
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection
