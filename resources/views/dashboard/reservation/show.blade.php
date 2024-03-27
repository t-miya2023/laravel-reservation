@extends('layouts.admin')
@section('content')

<div class="container">
    <h2 class="mb-3">予約確認</h2>
    <div class="row justify-content-center">
        <div class="col-10 d-flex flex-column">
            <div class="mb-3">
                <p class="fs-5">ご連絡先</p>
                <div class="bg-gray p-3 border rounded">
                    <p>お名前：{{ $reservation->user->name }}</p>
                    <p>メールアドレス：{{ $reservation->user->email }}</p>
                    <p>電話番号：{{ $reservation->user->tel }}</p>
                    <p>郵便番号：{{ $reservation->user->post_code }}</p>
                    <p>住所：{{ $reservation->user->address }}</p>
                </div>
            </div>
                <div class="row">
                    <div class="mb-3 col-12 col-sm-6">
                        <label class="form-label" for="checkin_date">チェックイン</label>
                        <input class="form-control" type="date" name="checkin_date" value="{{ $reservation->checkin_date }}" readonly>
                    </div>

                    <div class="mb-3 col-12 col-sm-6">
                        <label class="form-label" for="checkin_time">チェックイン予定時間</label>
                        <input class="form-control" type="time" name="checkin_time" step="1800" min="15:00" max="24:00" value="{{ $reservation->checkin_time }}" readonly>
                    </div>
                </div>
                
                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label" for="checkin_date">チェックアウト</label>
                        <input class="form-control" type="date" name="checkout_date" value="{{$reservation->checkout_date }}" readonly>
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label" for="number_of_room">部屋数</label>
                        <input id="number_of_room" class="form-control" type="number"  name="number_of_room" min="1" value="{{$reservation->number_of_room}}" readonly>
                    </div>
                </div>


                <div class="row">
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="total">宿泊人数</label>
                        <input class="form-control" type="number" name="total" value="{{ $reservation->total }}" readonly>
                    </div>
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="number_of_men">男性</label>
                        <input class="form-control" type="number" name="number_of_men" value="{{ $reservation->number_of_men }}" readonly>
                    </div>
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="number_of_women">女性</label>
                        <input class="form-control" type="number" name="number_of_women" value="{{ $reservation->number_of_women }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="dinner">夕食</label>
                        <input type="text" name="dinner" class="form-control" value="{{ $reservation->dinner }}" readonly>
                    </div>
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="breakfast">朝食</label>                        
                        <input type="text" name="breakfast" class="form-control" value="{{ $reservation->breakfast }}" readonly>
                    </div>
                </div>

                <div class="mb-3 col-6">
                    <label class="form-label" for="payment_info">お支払い方法</label>
                    <input type="text" name="payment_info" class="form-control" value="{{ $reservation->payment_info }}" readonly>
                </div>
                
                <div class="mb-3">
                    <label class="form-label" for="remarks_column">備考欄</label>
                    <textarea class="form-control" name="remarks_column" cols="20" rows="5" readonly>{{ $reservation->remarks_column }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">部屋情報</label>
                    @foreach ($reservation->reservation_details as $detail)
                    <div class="bg-gray p-3 border rounded mb-2">
                        <p>お部屋：
                                {{ $detail->room->name }}
                        </p>
                        <p>ベッド：
                                {{ $detail->room->bed_size }}
                        </p>
                        <p>定員：
                                {{ $detail->room->capacity }}
                        </p>
                        <p>
                                {{ $detail->room->smorking }}
                        </p>
                        <p>ご宿泊人数:
                                {{ $detail->number_of_guests }}
                        </p>
                    </div>
                    @endforeach
                </div>
                <div class="mb-5">
                    <p class="fs-3">総額：{{ number_format($reservation->reservation_fee) }}円(税込)</p>
                </div>
                <div>
                    <button class="btn btn-warning" onclick="history.back()">戻る</button>
                </div>
        </div>      
    </div>
</div>
@endsection
