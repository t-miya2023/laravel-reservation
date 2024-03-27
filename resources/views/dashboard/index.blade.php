@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="row">
            <a class="btn btn-primary col-3 mx-2" href="{{ route('room.index') }}">部屋情報編集</a>
            <a class="btn btn-info col-3 mx-2"href="{{ route('dashboard.user.index') }}">ユーザー情報一覧</a>
            <a class="btn btn-success col-3 mx-2"href="{{ route('dashboard.reservation.index') }}">予約情報一覧</a>
        </div>

        <div class="row">
            
            @if($reservations->isEmpty())
                <div class="text-center m-3"> 
                    <h2>本日のご予約はありません</h2>
                </div>  
            @else 
                <h2 class="m-3">本日のご予約</h2>
                @foreach($reservations as $key => $reservation)
            <div class="border border-info  border-3 p-3 m-3 bg-white text-center table-responsive">
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
                </table>
            </div>
            @endforeach
            @endif
        </div>

        <div class="d-flex">
            <span class="color-span"></span><p class="color-text">4人以下</p>
            <span class="color-span five-people"></span><p class="color-text">5人以上</p>
            <span class="color-span ten-people"></span><p class="color-text">10人以上</p>
        </div>

    </div>

    <div style="width: 80%;margin: auto;margin-top:50px;max-width:1000px">
        <div id='admin'></div>
    </div>
@endsection
@section('script')
<script src="{{asset('js/adminCalendar.js')}}"></script>
@endsection