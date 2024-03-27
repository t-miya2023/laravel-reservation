@extends('layouts.admin')
@section('content')
<div class="container">
    <h2 class="mb-3">部屋情報管理</h2>
    <a class="btn btn-primary mb-3" href="{{ route('room.create') }}">新規登録</a>
    <a class="btn btn-warning mb-3 mx-3" href="{{ route('dashboard.index') }}">ダッシュボードに戻る</a>
    <div class="row justify-content-center border-top  pt-3">
        <div class="col-12 d-flex flex-column align-items-center">
            @foreach ($rooms as $room)
                <div class="row bg-white p-3 mb-3 mw-800">

                    <div class="d-flex justify-content-between border-bottom mb-2">
                        <h3>{{ $room->name }}</h3>  
                        <a class="btn btn-primary mb-1" href="{{ route('room.edit',$room) }}">編集</a>
                    </div>
                
                    <div class="col-md-3 col-12">
                        <div class="room-img-box">
                        @if($room->img)
                            <img class="img-fluid mb-2 room-img" src="{{ asset('storage/room_img/' . $room->img) }}" alt="部屋の写真">
                        @else
                            <img class="img-fluid mb-2" src="{{ asset('images/no-img-reservation.webp') }}" alt="部屋の写真">
                        @endif
                        </div>
                    </div>  
                    <div class="col-md-9 col-12 ">
                        <table>
                            <tr>
                                <th>定員</th>
                                <td>{{ $room->capacity }}</td>
                            </tr>
                            <tr>
                                <th>１日あたりの宿泊価格</th>
                                <td>{{ number_format($room->price) }}円</td>
                            </tr>
                            <tr>
                                <th>{{ $room->smorking }}</th>
                            </tr>
                            <tr>
                                <th>ベッドサイズ</th>
                                <td>{{ $room->bed_size }}</td>
                            </tr>
                            <tr>
                                <th>利用状況</th>
                                <td>{{ $room->status === 0 ? "利用可能" : "利用不可" }}</td>
                            </tr>
                        </table>
                        
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection