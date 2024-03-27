@extends('layouts.app')
@section('content')

<div class="container">
    <h2 class="mb-3 heading-2">予約</h2>
    <div class="row justify-content-center">
        <div class="col-10 d-flex flex-column">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <form method="POST" action="{{ route('reservation.showroom') }}" enctype='multipart/form-data'>
                @csrf
                <div class="row">
                    <div class="mb-3 col-12 col-sm-6">
                        <label class="form-label" for="checkin_date">チェックイン</label>
                        <input id="checkin_date" class="form-control" type="date" name="checkin_date" value="{{ $date }}" readonly>
                    </div>

                    <div class="mb-3 col-12 col-sm-6">
                        <label class="form-label" for="checkin_time">チェックイン予定時間</label>
                        <input id="checkin_time" class="form-control" type="time" name="checkin_time" step="3600" min="15:00" max="24:00" value="15:00"  list="data-list">
                        <datalist id="data-list">
                            @for($i = 15; $i <= 23; $i++)
                                <option value="{{ $i }}:00">{{ $i }}:00</option>
                            @endfor
                        </datalist>
                        @error('checkin_time')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-6">
                        <label class="form-label" for="checkout_date">チェックアウト</label>
                        <input id="checkout_date" class="form-control" type="date"  name="checkout_date" value="{{ old('checkout_date') }}">
                        @error('checkout_date')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-3 col-6">
                        <label class="form-label" for="number_of_room">部屋数</label>
                        <input id="number_of_room" class="form-control" type="number"  name="number_of_room" min="1" value="1">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6 col-sm-3">
                        <label class="form-label" for="total">宿泊人数</label>
                        <input id="total" class="form-control" type="number" name="total" min="1" value="{{ null !== old('total') ? old('total') : 0 }}">
                        @error('total')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <div id="error-message" style="color: red;"></div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <label class="form-label" for="number_of_men">男性</label>
                        <input id="number_of_men" class="form-control" type="number" name="number_of_men" min="0" value="{{ null !== old('number_of_men') ? old('number_of_men') : 0 }}">
                        @error('number_of_men')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-6 col-sm-3">
                        <label class="form-label" for="number_of_women">女性</label>
                        <input id="number_of_women" class="form-control" type="number" name="number_of_women" min="0" value="{{ null !== old('number_of_women') ? old('number_of_women') : 0 }}">
                        @error('number_of_women')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="dinner">夕食</label>
                        <select id="dinner" name="dinner" class="form-control">
                            <option value="必要">必要</option>
                            <option value="不要">不要</option>
                        </select>
                    </div>
                    <div class="mb-3 col-6 col-sm-3">
                        <label class="form-label" for="breakfast">朝食</label>                        
                        <select id="breakfast" name="breakfast" class="form-control">
                            <option value="必要">必要</option>
                            <option value="不要">不要</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 col-6">
                    <label class="form-label" for="payment_info">お支払い方法</label>
                    <select id="payment_info" name="payment_info" class="form-control">
                        <option value="現地で現金払い">現地で現金払い</option>
                        <option value="クレジットカード決済">クレジットカード決済</option>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="form-label" for="remarks_column">備考欄</label>
                    <textarea id="remarks_column" class="form-control" name="remarks_column" cols="20" rows="5"></textarea>
                </div>

                <button id="submit-btn"type="submit" class="btn btn-primary">確認</button>
                <a class="btn btn-warning mx-5" href="{{route('reservation.index')}}">戻る</a>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{asset('js/formValidation.js')}}"></script>
@endsection