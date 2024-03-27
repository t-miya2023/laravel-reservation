@extends('layouts.admin')
@section('content')

<div class="container">
    @if(session('delete'))
        <div class="alert alert-success">
            {{ session('delete') }}
        </div>
    @endif
    @if (session('update'))
        <div class="alert alert-success">
            {{ session('update') }}
        </div>
    @endif
    <div>
        <h2>ご予約状況</h2>
        <a class="btn btn-warning mb-3" href="{{ route('dashboard.index') }}">ダッシュボードに戻る</a>
        <div class="row">
            <h3>日付検索</h3>
            <form class="mb-5" id="search-form" action="{{ route('dashboard.reservation.index') }}" method="GET">
                <div class="form-group col-6 mb-3">
                    <label for="search_date">検索ユーザー：</label>
                    <input type="text" name="search_user" id="search" class="form-control" value="{{ request('search_user') }}" placeholder="ユーザーIDかユーザー名で検索">
                </div>
                <div class="form-group col-6 mb-3">
                    <label for="search_date">検索日付：</label>
                    <input type="date" name="search_date" id="search_date" class="form-control" value="{{ request('search_date') }}">
                </div>
                <button type="submit" class="btn btn-primary mb-2">絞り込む</button>
                <button type="button" class="btn btn-danger mb-2" id="reset-btn">リセット</button>
            </form>
            @if($reservations->isEmpty())
                <div class="text-center"> 
                    <h2>ご予約はありません</h2>
                </div>  
            @else
            @foreach($reservations as $key => $reservation)
            @if ($key > 0)
                @if ($reservation->checkin_date != $reservations[$key - 1]->checkin_date)
                    <div class="my-5 border-bottom border-3"></div>
                    <p class="fs-3">{{ $reservation->checkin_date }}の予約一覧</p>
                @endif
            @else
                @if($reservation->checkin_date == $currentDate)
                    <p class="fs-3">本日の予約一覧</p>
                @else
                    <p class="fs-3">{{ $reservation->checkin_date }}の予約一覧</p>
                @endif
            @endif
            <div class="border border-info  border-3 p-3 mb-3 bg-white text-center table-responsive">
                <table class="reservation-table teble">
                    <tr>
                        <th>代表者氏名</th>
                        <th>予約ID</th>
                        <th>宿泊人数</th>
                        <th>チェックイン</th>
                        <th>チェックイン時間</th>
                        <th>チェックアウト</th>
                        <th>部屋</th>
                        <th>支払い方法</th>
                        <th>支払い状態</th>
                    </tr>
                    <tr>
                        <td>{{ $reservation->user->name }}</td>
                        <td>{{ $reservation->id }}</td>
                        <td>{{ $reservation->total }}</td>
                        <td>{{ $reservation->checkin_date }}</td>
                        <td>{{ $reservation->checkin_time }}</td>
                        <td>{{ $reservation->checkout_date }}</td>
                        <td>
                        @foreach ($reservation->reservation_details as $detail)
                            {{ $detail->room->name }}<br>
                        @endforeach
                        </td>
                        <td>{{ $reservation->payment_info}}</td>
                        @if( $reservation->payment_status === 0)
                            <td>未</td>
                        @elseif($reservation->payment_status === 1)
                            <td>済</td>
                        @endif
                    </tr>
                    <tr class="border-top">
                        <td>
                            <a class="btn btn-outline-info my-2" href="{{ route('dashboard.reservation.show',$reservation->id) }}">予約詳細</a>
                            <a class="btn btn-outline-info my-2" href="{{ route('dashboard.reservation.edit',$reservation->id) }}">予約内容を変更する</a>
                        </td>
                    </tr>
                </table>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/reset-btn.js')}}"></script>
@endsection