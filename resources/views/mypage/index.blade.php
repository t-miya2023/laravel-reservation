@extends('layouts.app')
@section('content')

<div class="container">
        @if(session('update'))
            <div class="alert alert-success">
                {{ session('update') }}
            </div>
        @elseif(session('delete'))
            <div class="alert alert-success">
                {{ session('delete') }}
            </div>
        @elseif(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
    <div class="row p-2">
        <div class="col-lg-4 col-12">
        <h2 class="heading-2 mx-3">ユーザー情報</h2>
            <div class="border border-info border-3 p-3 bg-white mb-5">
            <table class="mb-2">
                <tr>
                    <th>氏名：</th>
                    <td>{{ $user->name }} 様</td>
                </tr>
                <tr>
                    <th>メールアドレス：</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>電話番号：</th>
                    <td>{{ $user->tel }}</td>
                </tr>
                <tr>
                    <th>郵便番号：</th>
                    <td>{{ $user->post_code }}</td>
                </tr>
                <tr>
                    <th>住所：</th>
                    <td>{{ $user->address }}</td>
                </tr>
            </table>
            <a class="btn btn-outline-info my-2" href="{{ route('mypage.edit',$user->id) }}">ユーザー情報を編集</a>
            <a class="btn btn-outline-info my-2" href="{{ route('mypage.change_password',$user->id) }}">パスワードを変更する</a>
            </div>
        </div>
        <div class="col-lg-8 col-12">
            @if($reservations->isEmpty())
            <div class="text-center"> 
                <h2>ご予約はありません</h2>
            </div>  
            @else
                <h2 class="heading-2 mx-3">ご予約状況</h2>
            @endif
            @foreach($reservations as $reservation)
            <div class="border border-info  border-3 p-3 mb-3 bg-white">
                <p class="fs-3">{{ $reservation->checkin_date }}〜{{ $reservation->checkout_date }}</p>
                    <div class="table-responsive">
                <table class="teble">
                        <tr>
                            <th>ご宿泊人数：</th>
                            <td class="px-3">{{ $reservation->total }}名</td>
                        </tr>
                        <tr>
                            <th>お部屋：</th>
                            <td>
                            @foreach ($reservation->reservation_details as $detail)
                                <span class="px-3">{{ $detail->room->name }}</span>
                            @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>ベッドタイプ：</th>
                            <td>
                            @foreach ($reservation->reservation_details as $detail)
                                <span class="px-3">{{ $detail->room->bed_size }}</span>
                            @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                            @foreach ($reservation->reservation_details as $detail)
                                <span class="px-3">{{ $detail->room->smorking }}</span>
                            @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>ご夕食：</th>
                            <td class="px-3">{{ $reservation->dinner }}</td>
                        </tr>
                        <tr>
                            <th>ご朝食：</th>
                            <td class="px-3">{{ $reservation->breakfast }}</td>
                        </tr>
                        <tr>
                            <th>お支払い情報：</th>
                            <td class="px-3">{{ $reservation->payment_info }}</td>
                        </tr>
                        <tr>
                            <th>お支払い状況</th>
                            <td class="px-3">{{ $reservation->payment_status == 0 ? "未" : "済" }}</td>
                        </tr>
                        <tr>
                            <th>お支払い総額：</th>
                            <td class="fs-4">税込 {{  number_format($reservation->reservation_fee) }} 円</td>
                        </tr>
                    </table>
                    </div>
                <a class="btn btn-outline-info my-2" href="{{ route('reservation.show',$reservation->id) }}">予約詳細</a>
                <a class="btn btn-outline-info my-2" href="{{ route('reservation.edit',$reservation->id) }}">予約内容を変更する</a>
                @if($reservation->payment_info == "クレジットカード決済")
                    <a class="btn btn-outline-info my-2" href="{{ route('payment.create',$reservation->id) }}" {{ $reservation->payment_status == 1 ? 'style=pointer-events:none;opacity:0.5 ' : '' }}>決済する</a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection