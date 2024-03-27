<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {   

        $currentDate = Carbon::now()->toDateString(); //現在の日付を取得
        
        $query = Reservation::with('user', 'reservation_details.room')
                                    ->where('checkin_date', $currentDate);
        //チェックイン順に並び替え
        $reservations = $query->orderBy('checkin_time')->get();

        //日付のフォーマットを変更する
        foreach($reservations as $reservation){
            //チェックイン
            $checkInDate = Carbon::parse($reservation->checkin_date)->isoFormat('YYYY年MM月DD日');
            //フォーマットしたものを再度セットする
            $reservation->checkin_date = $checkInDate;
            //時間のフォーマット
            $checkInTime =  Carbon::parse($reservation->checkin_time)->isoFormat('HH:00');
            //フォーマットしたものを再度セットする
            $reservation->checkin_time = $checkInTime;
            //チェックアウト
            $checkOutDate = Carbon::parse($reservation->checkout_date)->isoFormat('YYYY年MM月DD日');
            //フォーマットしたものを再度セットする
            $reservation->checkout_date = $checkOutDate;
            //部屋情報を配列に入れていく
        }


        return view('dashboard.index',compact('reservations'));

    }
    
}
