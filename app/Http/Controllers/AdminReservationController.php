<?php

namespace App\Http\Controllers;
use App\Models\Reservation;
use App\Models\ReservationDetail;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentService;

class AdminReservationController extends Controller
{
    public function index(Request $request)
    {   

        $currentDate = Carbon::now()->toDateString(); //現在の日付を取得

        $searchUser = $request->input('search_user');
        $searchDate = $request->input('search_date');
        
        $query = Reservation::with('user', 'reservation_details.room')
                                    ->where('checkin_date', '>=', $currentDate)
                                    ->whereHas('user',function($query) use ($searchUser){
                                        $query->where('id','like',"%$searchUser%")
                                            ->orwhere('name','like',"%$searchUser%");
                                    });
        //検索フォームが入力されていたらその日付で絞りこみ
        if(isset($searchDate)){
            $query->where('checkin_date','=',$searchDate);
        }
        //チェックイン順に並び替え
        $reservations = $query->orderBy('checkin_date')->get();

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

        return view('dashboard.reservation.index',compact('reservations','currentDate'));
    }

    public function show(Reservation $reservation)
    {

        $reservationId = $reservation->id;
        // 部屋情報を取得する
        $reservation = Reservation::with('user', 'reservation_details.room')->findOrFail($reservationId);
        //税抜価格を表示させるため
        //$taxEx = 1 - $room->tax * 0.01;  //1 - 税率
        //$feeTaxfree = $reservation->reservation_fee * $taxEx;

        return view('dashboard.reservation.show',compact('reservation'));

    }

    public function edit(Reservation $reservation,User $user)
    {

        return view('dashboard.reservation.edit',compact('reservation'));

    }

    public function editShowRoom(Request $request,Reservation $reservation)
    {
        $request->validate([
            'checkin_date' => 'required|date',
            'checkin_time' => 'required',
            'checkout_date' => 'required|date|after:checkin_date',
            'total' => 'required|min:1',
            'number_of_room' => 'required|min:1',
            'number_of_men' => 'required',
            'number_of_women' => 'required',
        ]);
        // フォームから送信されたデータをセッションに保存
        $request->session()->put('reservation_data', $request->all());
        //予約済みの部屋を除外する
        // フォームから送信されたデータを取得
        $checkInDate = $request->input('checkin_date');
        $checkOutDate = $request->input('checkout_date');
        $total = $request->input('total');
        $roomNumber = $request->input('number_of_room');
        $dinner = $request->input('dinner');
        $breakfast = $request->input('breakfast');
        
        // 予約の中から滞在期間が被っている予約IDを取得する
        $reservedReservationId = Reservation::where(function ($query) use ($checkInDate, $checkOutDate) {
        $query->where('checkin_date', '<', $checkOutDate)
            ->where('checkout_date', '>', $checkInDate);
        })->pluck('id')->toArray();

        // 予約済みの予約IDからリレーション先の部屋IDを取得
        $reservedRooms = ReservationDetail::whereIn('reservation_id',$reservedReservationId )
                        ->pluck('room_id')->toArray();

        //空き部屋の数より希望する部屋の数が多い場合、エラーを返す
        $roomsCount = Room::where('status',0)->count();//利用可能な部屋数
        $reservedRoomsCount = count(array_unique($reservedRooms));//予約されてる部屋の数
        $availableRoomsCount = $roomsCount - ($reservedRoomsCount - ($reservation->number_of_room));//空いてる部屋の数(自分が予約した数は除く)
        if($roomNumber > $availableRoomsCount){
            return redirect()->back()->with('error',"ご希望の部屋数をご用意できませんでした。(残り $availableRoomsCount 部屋)");
        }
        //予約のない部屋を取得
        $availableRooms = Room::whereNotIN('id',$reservedRooms) 
                        ->where('status',0)
                        ->get();
        $reservationId = $reservation->id;
        //現在予約中の部屋を表示させる
        $reservation = Reservation::with('reservation_details.room')->findOrFail($reservationId);
        //1人1泊の料金の計算
        $totalFee = [];
        foreach($availableRooms as  $key => $room){
            $tax = $room->tax * 0.01 + 1;  //税率 
            //食事を含む料金の計算
            if( $dinner === "必要" && $breakfast === "必要" ){
                $totalFee[$key] = ($room->price + $room->dinner_fee + $room->breakfast_fee) * $tax;
            }elseif( $dinner === "必要" && $breakfast === "不要" ){
                $totalFee[$key] = ($room->price + $room->dinner_fee) * $tax;
            }elseif( $dinner === "不要" && $breakfast === "必要" ){
                $totalFee[$key] = ($room->price + $room->breakfast_fee) * $tax;
            }else{
                $totalFee[$key] = ($room->price) * $tax;
            }
        }
        $currentFee = [];
            foreach($reservation->reservation_details as $key => $detail){
                $tax = $detail->room->tax * 0.01 + 1;  //税率 
                //食事を含む料金の計算
                if( $dinner === "必要" && $breakfast === "必要" ){
                    $currentFee[$key] = ($detail->room->price + $detail->room->dinner_fee + $detail->room->breakfast_fee) * $tax;
                }elseif( $dinner === "必要" && $breakfast === "不要" ){
                    $currentFee[$key] = ($detail->room->price + $detail->room->dinner_fee) * $tax;
                }elseif( $dinner === "不要" && $breakfast === "必要" ){
                    $currentFee[$key] = ($detail->room->price + $detail->room->breakfast_fee) * $tax;
                }else{
                    $currentFee[$key] = ($detail->room->price) * $tax;
                }
            }

        return view('dashboard.reservation.editshowroom', compact('availableRooms','reservation','roomNumber','total','totalFee','dinner','breakfast','currentFee'));
        
    }


    public function editconfirm(Request $request,Reservation $reservation)
    {
        // ユーザー情報を表示するため
        $user = $reservation->user;
        // セッションから予約データを取得
        $reservationData = session()->get('reservation_data');
        //宿泊価格の計算
        $checkInDate = $reservationData['checkin_date']; //チェックイン日時
        $checkOutDate = $reservationData['checkout_date']; //チェックアウト日時
        $total = $reservationData['total']; //宿泊人数
        $totalFee = 0;
        $taxExFee = 0;
        $reservationType = $request->input('reservation_type');
    //-----1部屋の場合---------------------------------------------------------------------
        if($reservationType == "single"){
            //部屋情報を取得
            $roomId = $request->input('room_id');
            $room = Room::findOrFail($roomId);
            $tax = $room->tax * 0.01 + 1;  //税率 
            $stayDays = ((strtotime($checkOutDate) - strtotime($checkInDate)) / 86400); //滞在日数の計算
            //食事を含む料金の計算
            if( $reservationData['dinner'] === "必要" && $reservationData['breakfast'] === "必要" ){
                $taxEx = ($room->price + $room->dinner_fee + $room->breakfast_fee ) * $stayDays * $total;
            }elseif( $reservationData['dinner'] === "必要" && $reservationData['breakfast'] === "不要" ){
                $taxEx = ($room->price + $room->dinner_fee ) * $stayDays * $total;
            }elseif( $reservationData['dinner'] === "不要" && $reservationData['breakfast'] === "必要" ){
                $taxEx = ($room->price + $room->breakfast_fee ) * $stayDays * $total;
            }else{
                $taxEx = $room->price * $stayDays * $total;
            }
            //税込価格の計算
            $fee = $taxEx * $tax;
            $taxExFee = $taxEx;
            $totalFee = $fee;
            return view('dashboard.reservation.editconfirm',compact('room','reservationData','totalFee','taxExFee','user','reservationType','reservation'));
    //--------２部屋以上の場合--------------------------------------------------------------------
        }else{
            $roomIds = $request->input('selected_rooms');
            $rooms = Room::whereIn('id', $roomIds)->get();
            $guests = $request->input('number_of_guests');

            foreach($rooms as $key => $room){
            
                $tax = $room->tax * 0.01 + 1;  //税率 
                $stayDays = ((strtotime($checkOutDate) - strtotime($checkInDate)) / 86400); //滞在日数の計算
                $guest = $guests[$key]; //部屋ごとの宿泊人数
                //食事を含む料金の計算
                if( $reservationData['dinner'] === "必要" && $reservationData['breakfast'] === "必要" ){
                    $taxEx = ($room->price + $room->dinner_fee + $room->breakfast_fee )* $stayDays * $guest;
                }elseif( $reservationData['dinner'] === "必要" && $reservationData['breakfast'] === "不要" ){
                    $taxEx = ($room->price + $room->dinner_fee )* $stayDays * $guest;
                }elseif( $reservationData['dinner'] === "不要" && $reservationData['breakfast'] === "必要" ){
                    $taxEx = ($room->price + $room->breakfast_fee )* $stayDays * $guest;
                }else{
                    $taxEx = $room->price * $stayDays * $guest;
                }
                //税込価格の計算
                $fee = $taxEx * $tax;
                $taxExFee += $taxEx;
                $totalFee += $fee;
            }
            return view('dashboard.reservation.editconfirm',compact('rooms','reservationData','totalFee','taxExFee','user','reservationType','reservation','guests'));
        }
    }

    //Dependency Injection, DI PaymentServiceクラスのインスタンスを、別のクラスの内部で使用するため
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function update(Request $request, Reservation $reservation,ReservationDetail $reservationDetail)
    {
        if($reservation->payment_status == 1){
            try {
                $this->paymentService->refund($reservation->payment_number);
                
                $message = '変更前の予約の返金処理が行われました。';
            } catch (\Exception $e) {
                
                $message = '返金処理に失敗しました。: ' . $e->getMessage() . ' Reservation cancelled without refund.';
            }
        }

        $reservation->user_id = $reservation->user->id;
        $reservation->checkin_date = $request->input('checkin_date');
        $reservation->checkin_date = $request->input('checkin_date');
        $reservation->checkin_time = $request->input('checkin_time');
        $reservation->checkout_date = $request->input('checkout_date');
        $reservation->total = $request->input('total');
        $reservation->number_of_room = $request->input('number_of_room');
        $reservation->number_of_men = $request->input('number_of_men');
        $reservation->number_of_women = $request->input('number_of_women');
        $reservation->dinner = $request->input('dinner');
        $reservation->breakfast = $request->input('breakfast');
        $reservation->payment_info = $request->input('payment_info');
        $reservation->reservation_fee = $request->input('reservation_fee');
        $reservation->remarks_column = $request->input('remarks_column');
        $reservation->payment_status = 0;

        $reservation->save();

        $reservationType = $request->input('reservation_type');
    //--------1部屋の場合-------------------------------------------------------------------
        if($reservationType == "single"){
            //対象の予約（Detail）を１度削除し
            ReservationDetail::where('reservation_id',$reservation->id)->delete();
            //再度予約（Detail）を作成し直す
            $reservationDetail = new ReservationDetail();
            $reservationDetail->reservation_id = $reservation->id;
            $reservationDetail->room_id = $request->input('room_id');
            $reservationDetail->number_of_guests = $request->input('total');
            $reservationDetail->save();
    //---------2部屋以上の場合---------------------------------------------------------------
        }else{
            //対象の予約（Detail）を１度削除し
            ReservationDetail::where('reservation_id',$reservation->id)->delete();
            //再度予約（Detail）を作成し直す
            $roomIds = $request->input('room_ids');
            $guests = $request->input('number_of_guests');
            foreach ($roomIds as $key => $roomId) {
                $reservationDetail = new ReservationDetail();
                $reservationDetail->reservation_id = $reservation->id;
                $reservationDetail->room_id = $roomId;
                $reservationDetail->number_of_guests = $guests[$key];
                $reservationDetail->save();
            }
        }
        $userName = $reservation->user->name;
        if(isset($message)){
            return redirect()->route('dashboard.reservation.index')->with('update',"$message  $userName 様の予約は正常に変更されました。 ");
        }else{
            return redirect()->route('dashboard.reservation.index')->with('update',"$userName 様の予約は正常に変更されました。 ");
        }

    }

    public function destroy(Reservation $reservation)
    {
        if($reservation->payment_status == 1){
            try {
                $this->paymentService->refund($reservation->payment_number);
                
                $message = 'ご予約は正常にキャンセルされ、返金処理が行われました。';
            } catch (\Exception $e) {
                
                $message = '返金処理に失敗しました。: ' . $e->getMessage() . ' Reservation cancelled without refund.test';
            }
        }else{
            $message = 'ご予約は正常にキャンセルされました。';
        }

        $reservation->delete();

        return redirect()->route('dashboard.reservation.index')->with('delete',$message);
    }
}
