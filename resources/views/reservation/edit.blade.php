@extends('layouts.app')
@section('content')

<div class="container">
    <h2 class="mb-3 heading-2">予約変更</h2>
    <div class="row justify-content-center">
        <div class="col-10 d-flex flex-column">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <form method="POST" action="{{ route('reservation.editshowroom',$reservation->id) }}" enctype='multipart/form-data'>
                @csrf
                <div class="row">
                    <div class="mb-3 col-12 col-sm-6">
                        <label class="form-label" for="checkin_date">チェックイン</label>
                        <input class="form-control" type="date" name="checkin_date" value="{{ $reservation->checkin_date }}">
                        @error('checkin_date')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3 col-12 col-sm-6">
                        <label class="form-label" for="checkin_time">チェックイン予定時間</label>
                        <input id="checkin_time" class="form-control" type="time" name="checkin_time" step="3600" min="15:00" max="24:00" value="{{ $reservation->checkin_time }}"  list="data-list">
                        <datalist id="data-list">
                            @for($i = 15; $i <= 23; $i++)
                                <option value="{{ $i }}:00">{{ $i }}:00</option>
                            @endfor
                        </datalist>                        @error('checkin_time')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label" for="checkin_date">チェックアウト</label>
                        <input class="form-control" type="date" name="checkout_date" value="{{ $reservation->checkout_date }}">
                        @error('checkout_date')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label" for="number_of_room">部屋数</label>
                        <input id="number_of_room" class="form-control" type="number"  name="number_of_room" min="1" value="{{$reservation->number_of_room}}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6 col-sm-3">
                        <label class="form-label" for="total">宿泊人数</label>
                        <input id="total" class="form-control" type="number" name="total" min="0" value="{{ $reservation->total }}">
                        <div id="error-message" style="color: red;"></div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <label class="form-label" for="number_of_men">男性</label>
                        <input class="form-control" type="number" name="number_of_men" min="0" value="{{ $reservation->number_of_men }}">
                    </div>
                    <div class="col-6 col-sm-3">
                        <label class="form-label" for="number_of_women">女性</label>
                        <input class="form-control" type="number" name="number_of_women" min="0" value="{{ $reservation->number_of_women }}">
                    </div>
                    @error('total')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                    @error('number_of_men')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                    @error('number_of_women')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row">
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="dinner">夕食</label>
                        <select name="dinner" class="form-control">
                            <option value="必要" {{ $reservation->dinner === "必要" ? 'selected ': '' }}>必要</option>
                            <option value="不要" {{ $reservation->dinner === "不要" ? 'selected ': '' }}>不要</option>
                        </select>
                    </div>
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="breakfast">朝食</label>                        
                        <select name="breakfast" class="form-control">
                            <option value="必要" {{ $reservation->breakfast === "必要" ? 'selected ': '' }}>必要</option>
                            <option value="不要" {{ $reservation->breakfast === "不要" ? 'selected ': '' }}>不要</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 col-6">
                    <label class="form-label" for="payment_info">お支払い方法</label>
                    <select name="payment_info" class="form-control">
                        <option value="現地で現金払い" {{ $reservation->payment_info === "現地で現金払い" ? 'selected ': '' }}>現地で現金払い</option>
                        <option value="クレジットカード決済" {{ $reservation->payment_info === "クレジットカード決済" ? 'selected ': '' }}>クレジットカード決済</option>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="form-label" for="remarks_column">備考欄</label>
                    <textarea class="form-control" name="remarks_column" cols="20" rows="5">{{ $reservation->remark_column }}</textarea>
                </div>

                <button id="submit-btn" type="submit" class="btn btn-primary">変更する</button>
                <a class="btn btn-warning mx-5" onclick="history.back()">戻る</a>
            </form>
            <div class="mt-5 text-end">
            @include('reservation.delete')
                <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteReservationModal{{ $reservation->id }}">予約をキャンセルする</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/formValidation.js')}}"></script>
@endsection
