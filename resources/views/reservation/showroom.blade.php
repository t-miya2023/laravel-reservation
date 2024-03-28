@extends('layouts.app')
@section('content')


<div class="container">
    <div class="d-flex justify-content-between">
        <h2 class="mb-3 heading-2">部屋選択</h2>
        <a class="btn btn-warning align-item-center mb-3" onclick="history.back()">戻る</a>
    </div>
    <div class="row justify-content-center border-top  pt-3">
        <div class="col-12 d-flex flex-column align-items-center">
        
        <!--1部屋の場合-->
            @if(!(isset($roomNumber)))
                @foreach ($availableRooms as $key => $room)
                    <div class="row bg-white p-3 mb-3 mw-800">
                        <div class="d-flex justify-content-between border-bottom mb-2">
                            <h3 class="heading-3 mx-3">{{ $room->name }}</h3>  
                            <a class="btn btn-primary" href="{{ route('room.show',$room->id)  }}">詳細確認</a>
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
                                <tr class="fs-4">
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
                                    <th>夕食</th>
                                    <td>{{ $dinner == "必要" ? 'あり' : 'なし'}}</td>
                                </tr>
                                <tr>
                                    <th>朝食</th>
                                    <td>{{ $breakfast == "必要" ? 'あり' : 'なし'}}</td>
                                </tr>
                            </table>
                            <div class="float-end">
                                <p class="mb-1">大人１人/１泊の料金</p>
                                <p class="fs-3 text-danger text-align-right">{{ number_format($totalFee[$key]) }}円(税込)</p>
                            </div>
                        </div>
                        <form  id="roomField"action="{{ route('reservation.confirm') }}" method="post">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            <input type="hidden" name="reservation_type" value="single">
                            <button class="btn btn-outline-primary" type="submit">この部屋で予約する</button>
                        </form>
                    </div>
                @endforeach
            @else
            <!--2部屋以上の場合-->
            <div id="error-message" style="color: red;"></div>
            <form  id="roomField"action="{{ route('reservation.confirm') }}" method="post">
            @foreach ($availableRooms as $key => $room)
            @csrf
                    <div class="row bg-white p-3 mb-3 mw-800 room
                    " data-capacity="{{ $room->capacity }}">
                        <div class="d-flex justify-content-between border-bottom mb-2">
                            <h3 class="heading-3 mx-3">{{ $room->name }}</h3>  
                            <a class="btn btn-primary" href="{{ route('room.show',$room->id)  }}">詳細確認</a>
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
                        <div class="col-md-9 col-12">
                            <table>
                                <tr class="fs-4">
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
                                    <th>夕食</th>
                                    <td>{{ $dinner == "必要" ? 'あり' : 'なし'}}</td>
                                </tr>
                                <tr>
                                    <th>朝食</th>
                                    <td>{{ $breakfast == "必要" ? 'あり' : 'なし'}}</td>
                                </tr>
                            </table>
                            <div class="float-end">
                                <p class="mb-1">大人１人/１泊の料金</p>
                                <p class="fs-3 text-danger text-align-right">{{ number_format($totalFee[$key]) }}円(税込)</p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <input type="checkbox" class="btn-check select-btn" id="room{{$room->id}}" name="selected_rooms[]" value="{{ $room->id }}" data-capacity="{{ $room->capacity }}">
                            <label class="btn btn-outline-primary" for="room{{ $room->id }}">この部屋で予約する</label>
                        </div>
                        <div class="col-7">
                            <select class="form-select" name="number_of_guests[]" aria-label="Default select example" disabled>
                                <option selected>この部屋に宿泊する人数を入力してください</option>
                            </select>
                        </div>
                        
                    </div>
                @endforeach
                <input type="hidden" name="reservation_type" value="multi">
                <div id="error-message2" style="color: red;"></div>
                <button class="btn btn-primary submit-btn" type="submit" disabled>選択した部屋で予約する</button>
            </form>
            @endif
        </div>
    </div>
</div>

@endsection

@if(isset($roomNumber))
@section('script')
<script>
    var maxSelectedRooms = {{ $roomNumber }}; // 最大選択可能な部屋数
    var total = {{ $total }}; //予約人数
</script>
<script src="{{asset('js/roomFilter.js')}}"></script>
@endsection
@endif