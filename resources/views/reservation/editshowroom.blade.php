@extends('layouts.app')
@section('content')


<div class="container">
    <h2 class="mb-3 heading-2">部屋選択</h2>
    <div class="row justify-content-center border-top  pt-3">
        <div class="col-12 d-flex flex-column align-items-center">

<!--1部屋の場合------------------------------------------------------------------------------------------>
                @if($roomNumber  == 1)
                    @foreach ($reservation->reservation_details as $key => $detail)
                    <div class="row bg-white p-3 mb-3 mw-800 border border-warning border-3 position-relative room now-room" data-capacity="{{ $detail->room->capacity }}">
                        <p class="fs-5 bg-white border border-warning border-3 absolute text-center">現在予約中の部屋</p>
                        <div class="d-flex justify-content-between border-bottom mb-2">
                            <h3>{{ $detail->room->name }}</h3> 
                            <a class="btn btn-primary" href="{{ route('room.show',$detail->room->id)  }}">詳細確認</a>
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="room-img-box">
                            @if($detail->room->img)
                                <img class="img-fluid mb-2 room-img" src="{{ asset('storage/room_img/' . $detail->room->img) }}" alt="部屋の写真">
                            @else
                                <img class="img-fluid mb-2" src="{{ asset('images/no-img-reservation.webp') }}" alt="部屋の写真">
                            @endif
                            </div>
                        </div>  
                        <div class="col-md-9 col-12 ">
                        <table>
                                <tr class="fs-4">
                                    <th>定員</th>
                                    <td>{{ $detail->room->capacity }}</td>
                                </tr>
                                <tr>
                                    <th>{{ $detail->room->smorking }}</th>
                                </tr>
                                <tr>
                                    <th>ベッドサイズ</th>
                                    <td>{{ $detail->room->bed_size }}</td>
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
                                <p class="fs-3 text-danger text-align-right">{{ number_format($currentFee[$key]) }}円(税込)</p>
                            </div>
                        </div>
                        <form action="{{ route('reservation.editconfirm',$reservation->id) }}" method="post">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $detail->room->id }}">
                            <input type="hidden" name="reservation_type" value="single">
                            <button class="btn btn-primary" type="submit">この部屋で予約する</button>
                            <button class="submit-btn" style="display:none"></button>
                        </form>
                    </div>
                @endforeach
                @foreach ($availableRooms as $key => $room)
                    <div class="row bg-white p-3 mb-3 mw-800 room" data-capacity="{{ $room->capacity }}">
                        <div class="d-flex justify-content-between border-bottom mb-2">
                            <h3>{{ $room->name }}</h3>  
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
                        <form action="{{ route('reservation.editconfirm',$reservation->id) }}" method="post">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            <input type="hidden" name="reservation_type" value="single">
                            <button class="btn btn-primary" type="submit">この部屋で予約する</button>
                        </form>
                    </div>
                @endforeach
<!--2部屋の場合------------------------------------------------------------------------------------------>
        @else
            <div id="error-message" style="color: red;"></div>
                <div id="error-message" style="color: red;"></div>
                <form  id="roomField"action="{{ route('reservation.editconfirm',$reservation->id) }}" method="post">
                    <!--予約中の部屋-->
                    @foreach ($reservation->reservation_details as $key => $detail)
                    <div class="row bg-white p-3 mb-3 mw-800 room border border-warning border-3 position-relative" data-capacity="{{ $detail->room->capacity }}">
                        <p class="fs-5 bg-white border border-warning border-3 absolute text-center">現在予約中の部屋</p>
                        <div class="d-flex justify-content-between border-bottom mb-2">
                            <h3>{{ $detail->room->name }}</h3>  
                            <a class="btn btn-primary" href="{{ route('room.show',$detail->room->id)  }}">詳細確認</a>
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="room-img-box">
                            @if( $detail->room->img)
                                <img class="img-fluid mb-2 room-img" src="{{ asset('storage/room_img/' . $detail->room->img) }}" alt="部屋の写真">
                            @else
                                <img class="img-fluid mb-2" src="{{ asset('images/no-img-reservation.webp') }}" alt="部屋の写真">
                            @endif
                            </div>
                        </div>  
                        <div class="col-md-9 col-12 ">
                        <table>
                                <tr class="fs-4">
                                    <th>定員</th>
                                    <td>{{ $detail->room->capacity }}</td>
                                </tr>
                                <tr>
                                    <th>{{ $detail->room->smorking }}</th>
                                </tr>
                                <tr>
                                    <th>ベッドサイズ</th>
                                    <td>{{ $detail->room->bed_size }}</td>
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
                                <p class="fs-3 text-danger text-align-right">{{ number_format($currentFee[$key]) }}円(税込)</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" class="btn-check select-btn" id="room{{$detail->room->id}}" name="selected_rooms[]" value="{{ $detail->room->id }}" data-capacity="{{ $detail->room->capacity }}">
                            <label class="btn btn-outline-primary" for="room{{ $detail->room->id }}">この部屋で予約する</label>
                        </div>
                        <div class="col-7">
                            <select class="form-select" name="number_of_guests[]" aria-label="Default select example" disabled>
                                <option selected>この部屋に宿泊する人数を入力してください</option>
                            </select>
                        </div>
                    </div>
                    @endforeach
                    @foreach ($availableRooms as $key => $room)
                    @csrf
                    <div class="row bg-white p-3 mb-3 mw-800 room" data-capacity="{{ $room->capacity }}">
                        <div class="d-flex justify-content-between border-bottom mb-2">
                            <h3>{{ $room->name }}</h3>  
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
                <button class="btn btn-primary submit-btn" type="submit">選択した部屋で予約する</button>
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
<script src="{{asset('js/editRoomFilter.js')}}"></script>
@endsection
@endif