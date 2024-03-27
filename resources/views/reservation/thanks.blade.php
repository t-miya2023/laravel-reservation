@extends('layouts.app')
@section('content')

<div class="container">
    @if(session('update'))
        <div class="alert alert-success">
            {{ session('update') }}
        </div>
    @endif
    <div class="text-center border p-5 bg-white ">
        <h2>ご予約ありがとうございます！</h2>
        <p>キャンセルは１週間前までとなります。</p>
        <p>クレジットカード決済はマイページから行えます。</p>
        <a class="btn btn-outline-success" href="{{ route('reservation.index') }}">トップページへ戻る</a>
        <a class="btn btn-outline-success" href="{{ route('mypage.index') }}">マイページ</a>
    </div>
</div>

@endsection