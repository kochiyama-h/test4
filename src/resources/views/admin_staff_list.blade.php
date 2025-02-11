@extends('layouts.app')

@section('title', 'スタッフ一覧 - COACHTECH')

@section('additional_css')
    <link href="{{ asset('css/admin_staff_list.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="staff-container">
    <h1 class="page-title">スタッフ一覧</h1>

    <div class="staff-table-wrapper">
        <table class="staff-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffMembers as $staff)
                <tr>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}" class="detail-link">
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