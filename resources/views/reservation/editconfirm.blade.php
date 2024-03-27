@extends('layouts.app')
@section('content')

<div class="container">
    <h2 class="mb-3 heading-2">予約確認</h2>
    <h3 class="text-center mb-3">＊以下の内容でご予約を変更します。<br>よろしければ確定ボタンを押してください。</h3>
    <div class="row justify-content-center">
        <div class="col-10 d-flex flex-column">
            <div class="mb-3">
                <p class="fs-5">ご連絡先</p>
                <div class="bg-gray p-3 border rounded">
                    <p>お名前：{{ $user->name }}</p>
                    <p>メールアドレス：{{ $user->email }}</p>
                    <p>電話番号：{{ $user->tel }}</p>
                    <p>郵便番号：{{ $user->post_code }}</p>
                    <p>住所：{{ $user->address }}</p>
                </div>
            </div>
            <form method="POST" action="{{route('reservation.update',$reservation->id)}}" enctype='multipart/form-data'>
                @csrf
                @method('Patch')
                <div class="row">
                    <div class="mb-3 col-12 col-sm-6">
                        <label class="form-label" for="checkin_date">チェックイン</label>
                        <input class="form-control" type="date" name="checkin_date" value="{{ $reservationData['checkin_date'] }}" readonly>
                    </div>

                    <div class="mb-3 col-12 col-sm-6">
                        <label class="form-label" for="checkin_time">チェックイン予定時間</label>
                        <input class="form-control" type="time" name="checkin_time" step="1800" min="15:00" max="24:00" value="{{ $reservationData['checkin_time'] }}" readonly>
                    </div>
                </div>
                
                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label" for="checkin_date">チェックアウト</label>
                        <input class="form-control" type="date" name="checkout_date" value="{{ $reservationData['checkout_date'] }}" readonly>
                    </div>
                    <div class="mb-3 col-6">
                            <label class="form-label" for="number_of_room">部屋数</label>
                        <input id="number_of_room" class="form-control" type="number"  name="number_of_room" min="1" value="{{ $reservationData['number_of_room'] }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="total">宿泊人数</label>
                        <input class="form-control" type="number" name="total" value="{{ $reservationData['total'] }}" readonly>
                    </div>
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="number_of_men">男性</label>
                        <input class="form-control" type="number" name="number_of_men" value="{{ $reservationData['number_of_men'] }}" readonly>
                    </div>
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="number_of_women">女性</label>
                        <input class="form-control" type="number" name="number_of_women" value="{{ $reservationData['number_of_women'] }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="dinner">夕食</label>
                        <input type="text" name="dinner" class="form-control" value="{{ $reservationData['dinner'] }}" readonly>
                    </div>
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="breakfast">朝食</label>                        
                        <input type="text" name="breakfast" class="form-control" value="{{ $reservationData['breakfast'] }}" readonly>
                    </div>
                </div>

                <div class="mb-3 col-6">
                    <label class="form-label" for="payment_info">お支払い方法</label>
                    <input type="text" name="payment_info" class="form-control" value="{{ $reservationData['payment_info'] }}" readonly>
                </div>
                
                <div class="mb-3">
                    <label class="form-label" for="remarks_column">備考欄</label>
                    <textarea class="form-control" name="remarks_column" cols="20" rows="5" readonly>{{ $reservationData['remarks_column'] }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">部屋情報</label>
                    @if($reservationType == "single")
                        <div class="bg-gray p-3 border rounded">
                            <p>お部屋：{{ $room->name }}</p>
                            <p>ベッド：{{ $room->bed_size }}</p>
                            <p>定員：{{ $room->capacity }}</p>
                            <p>{{ $room->smorking }}</p>
                        </div>
                        <input type="hidden" value="{{ $totalFee }}" name="reservation_fee">
                        <input type="hidden" value="{{ $room->id }}" name="room_id">
                        <input type="hidden" name="reservation_type" value="single">
                    @else
                        @foreach($rooms as $key => $room)
                        <div class="bg-gray p-3 border rounded mb-2">
                            <p>お部屋：{{ $room->name }}</p>
                            <p>ベッド：{{ $room->bed_size }}</p>
                            <p>定員：{{ $room->capacity }}</p>
                            <p>{{ $room->smorking }}</p>
                            <p>ご宿泊人数:{{$guests[$key]}}</p>
                        </div>
                        <input type="hidden" value="{{ $totalFee }}" name="reservation_fee">
                        <input type="hidden" value="{{ $room->id }}" name="room_ids[]">
                        <input type="hidden" value="{{ $guests[$key] }}" name="number_of_guests[]">
                        <input type="hidden" name="reservation_type" value="multi">
                        @endforeach
                    @endif
                </div>
                <div class="mb-5">
                <p class="fs-3">総額：{{ number_format($totalFee) }}円 (税抜：{{ number_format($taxExFee) }}円)</p>
                </div>
                    <input type="hidden" value="{{ $totalFee }}" name="reservation_fee">
                    <input type="hidden" value="{{ $room->id }}" name="room_id">
                    <button type="submit" class="btn btn-primary btn-lg">予約確定</button>
                    <a class="btn btn-warning btn-lg mx-3" onclick="history.back()">部屋一覧に戻る</a>
            </form>
        </div>
    </div>
</div>
@endsection
