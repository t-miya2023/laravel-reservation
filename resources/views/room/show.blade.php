@extends('layouts.app')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-3 heading-2">客室情報</h2>
        <a class="btn btn-warning" onclick="history.back()">戻る</a>
    </div>
    <div class="row justify-content-center border-top pt-3">
        <div class="col-12 mt-3">
                <h3 class="heading-3 mx-5">{{ $room->name }}</h3>  
                <div class="col-12 text-center my-5">
                    <div class="room">
                    @if($room->img)
                        <img class="img-fluid mb-2" style="height:400px" src="{{ asset('storage/room_img/' . $room->img) }}" alt="部屋の写真">
                    @else
                        <img class="img-fluid mb-2" style="height:400px"src="{{ asset('images/no-img-reservation.webp') }}" alt="部屋の写真">
                    @endif
                    </div>
                </div>  
                <h4>詳細</h4>
                <p class="p-3 border border-primary bg-white">{{ $room->detail}}</p>
                <h4>仕様</h4>
                <div class="col-12 p-3 border border-primary bg-white mb-3">
                    <table class="show-table">
                        <tr>
                            <th>定員</th>
                            <td>{{ $room->capacity }}</td>
                        </tr>
                        <tr>
                            <th>{{ $room->smorking }}</th>
                        </tr>
                        <tr>
                            <th>ベッドサイズ</th>
                            <td>{{ $room->bed_size }}</td>
                        </tr>
                        <tr>
                            <th>アメニティ</th>
                            <td>{{ $room->amenities }}</td>
                        </tr>
                        <tr>
                            <th>設備</th>
                            <td>{{ $room->facility }}</td>
                        </tr>
                    </table>
                </div>
                <h4>料金</h4>
                <div>
                    <table class="price-table">
                        <tr>
                            <th></th>
                            <th>素泊まりプラン</th>
                            <th>朝食付きプラン</th>
                            <th>夕食朝食付きプラン</th>
                        </tr>
                        <tr>
                            <th>１人１泊あたりの料金（税込）</th>
                            <td>{{ number_format($room->price * (($room->tax + 100)/100)) }}円</td>
                            <td>{{ number_format(($room->price + $room->breakfast_fee) * (($room->tax + 100)/100)) }}円</td>
                            <td>{{ number_format(($room->price + $room->breakfast_fee + $room->dinner_fee) * (($room->tax + 100)/100)) }}円</td>
                        </tr>
                    </table>
                </div>
            </div>
    </div>
</div>

@endsection