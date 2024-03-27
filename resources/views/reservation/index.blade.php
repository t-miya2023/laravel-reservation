@extends('layouts.app')
@section('content')
    
    <div id="carouselExample" class="carousel slide top-slider position-relative" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
            <img src="{{ asset('images/top1_new.webp') }}" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
            <img src="{{ asset('images/top2_new.webp') }}" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
            <img src="{{ asset('images/top3_new.webp') }}" class="d-block w-100" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        <div class="mv-thx" id="mv-thx">
            <p class="mv-thx-typing" id="mv-thx-typing">Welcome to Hotel SeaSide,<br>where relaxation and comfort await you!</p>
        </div>
    </div>
    <div class="container">
        <div class="text-center">
        <h2 class="my-5 heading-font">Rooms</h2>
        </div>
        <div class="row">
            @foreach($rooms as $room)
            <div class="col-lg-4 mb-3 text-left">
                <div class="card">
                    <img src="{{ asset('storage/room_img/' . $room->img) }}" class="card-img-top" alt="部屋のイメージ" style="height: 300px;object-fit:cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $room->name }}</h5>
                        <p class="card-text">{{ Str::limit($room->detail,100) }}</p>
                        <a href="{{ route('room.show',$room->id) }}" class="btn btn-primary">Show More</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @guest
    <div class="container text-center">
        <h2 class="my-5 heading-font-2">Registration <br>& Login</h2>
        <p class="text-center fs-3 m-3 font">まずはこちらからご登録してください</p>
        <div class="row my-5">
            <div class="col-12 d-flex justify-content-center">
            <a class="btn btn-outline-primary btn-block mx-2 btn-lg" href="{{ route('register') }}">{{ __('Register') }}</a>
            <a class="btn btn-outline-primary btn-block mx-2 btn-lg" href="{{ route('login') }}">{{ __('Login') }}</a>
            </div>
        </div>
    </div>
    @endguest
    <div class="container text-center">
        <h2 class="my-5 heading-font">Reservation</h2>
        <p class="text-center fs-3 m-3 font">以下より宿泊予定日を選択してください</p>

        <div style="width: 80%;margin: auto;margin-top:50px; max-width:1000px;">
            <div id='calendar'></div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{asset('js/calendar.js')}}"></script>

@endsection