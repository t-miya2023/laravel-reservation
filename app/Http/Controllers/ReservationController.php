<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationDetail;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::orderBy('id','ASC')->take(9)->get();

        return view('reservation.index',compact('rooms'));

    }

    public function calendar()
    {

        $reservations = Reservation::with(['reservation_details'])->get();

        $detailsCountMap = [];

        foreach ($reservations as $reservation) {
            foreach ($reservation->reservation_details as $detail) {
                $checkinDate = new Carbon($reservation->checkin_date);
                $checkoutDate = new Carbon($reservation->checkout_date);
    
                while ($checkinDate < $checkoutDate) {
                    $dateString = $checkinDate->toDateString();
    
                    if (isset($detailsCountMap[$dateString])) {
                        $detailsCountMap[$dateString]++;
                    } else {
                        $detailsCountMap[$dateString] = 1;
                    }
    
                    $checkinDate->addDay();
                }
            }
        }

        $reservations = Reservation::all(['checkin_date', 'checkout_date']);
        $roomCount = Room::where('status',0)->count();
        return response()->json([
            'reservations' => $reservations,
            'roomCount' => $roomCount,
            'detailsCountMap' => $detailsCountMap
        ]);
    }

    public function adminCalendar()
    {
        $reservations = Reservation::with('user')->get();

        return response()->json($reservations);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create($date)
    {
        return view('reservation.create',compact('date'));
    }

    //予約可能な部屋一覧表示
    public function showRoom(Request $request)
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
        $roomsCount = Room::where('status',0)->count();//利用可能な部屋数
        $reservedRoomsCount = count(array_unique($reservedRooms));//予約されてる部屋の数
        $availableRoomsCount = $roomsCount - $reservedRoomsCount;//空いてる部屋の数
        //空き部屋の数より希望する部屋の数が多い場合、エラーを返す
        if($roomNumber > $availableRoomsCount){
            return redirect()->back()->with('error',"ご希望の部屋数をご用意できませんでした。(残り $availableRoomsCount 部屋)");
        }
        //2部屋以上必要な時
        if($roomNumber >= 2){
            //全部 JSで制御
            $availableRooms = Room::whereNotIN('id',$reservedRooms) //予約されている部屋と被らない部屋
                            ->where('status',0) //利用不可状態の部屋は非表示
                            ->get();

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
            //利用できる部屋が発見できない場合はフォームにリダイレクト
            if($availableRooms->isEmpty()){
                return redirect()->back()->with('error',"ご希望の部屋をご用意できませんでした。");
            }

            $availableRoomsCapa = Room::whereNotIN('id',$reservedRooms) //予約されている部屋と被らない部屋
            ->where('status',0) //利用不可状態の部屋は非表示
            ->pluck('capacity')->toArray();
            $availableRoomsTotalCapa = array_sum($availableRoomsCapa);
            if($availableRoomsTotalCapa < $total){
                return redirect()->back()->with('error',"ご希望の人数を収容できる部屋をご用意できませんでした。");
            }

            return view('reservation.showroom', compact('availableRooms','roomNumber','total','totalFee','dinner','breakfast'));
        
        //1部屋の時
        }else{
            // データベースから泊まれる部屋の一覧を取得するクエリを作成
            $availableRooms = Room::where('capacity', '=' , $total) //定員と宿泊人数が一致する部屋
                            ->whereNotIN('id',$reservedRooms) //予約されている部屋と被らない部屋
                            ->where('status',0) //利用不可状態の部屋は非表示
                            ->get();
            //もし部屋が見つからなければ
            if($availableRooms->isEmpty()){
                for($i=0;$i<30;$i++){
                    $total++;
                    $availableRooms = Room::where('capacity', '=' , $total)
                        ->whereNotIN('id',$reservedRooms)
                        ->where('status',0)
                        ->get();
                    // 利用可能な部屋が見つかった場合はループを抜ける
                    if (!$availableRooms->isEmpty()) {
                        break;
                    }
                }
            }
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
            //利用できる部屋が発見できない場合はフォームにリダイレクト
            if($availableRooms->isEmpty()){
                return redirect()->back()->with('error',"ご希望の部屋をご用意できませんでした。");
            }
                return view('reservation.showroom', compact('availableRooms','totalFee','dinner','breakfast'));
        }
    }

    //確認ページ
    public function confirm(Request $request)
    {
        // ユーザー情報を表示するため
        $user = Auth::user();
        // セッションから予約データを取得
        $reservationData = session()->get('reservation_data');
        
        //宿泊価格の計算
        $checkInDate = $reservationData['checkin_date']; //チェックイン日時
        $checkOutDate = $reservationData['checkout_date']; //チェックアウト日時
        $total = $reservationData['total']; //宿泊人数
        $totalFee = 0;
        $taxExFee = 0;
        $reservationType = $request->input('reservation_type');
        
        if($reservationType == "single"){
            $roomId = $request->input('room_id');
            $room = Room::findOrFail($roomId);
            $tax = $room->tax * 0.01 + 1;  //税率 
            $stayDays = ((strtotime($checkOutDate) - strtotime($checkInDate)) / 86400); //滞在日数の計算
            //食事を含む料金の計算
            if( $reservationData['dinner'] === "必要" && $reservationData['breakfast'] === "必要" ){
                $taxEx = ($room->price + $room->dinner_fee + $room->breakfast_fee )* $stayDays * $total;
            }elseif( $reservationData['dinner'] === "必要" && $reservationData['breakfast'] === "不要" ){
                $taxEx = ($room->price + $room->dinner_fee )* $stayDays * $total;
            }elseif( $reservationData['dinner'] === "不要" && $reservationData['breakfast'] === "必要" ){
                $taxEx = ($room->price + $room->breakfast_fee )* $stayDays * $total;
            }else{
                $taxEx = $room->price * $stayDays * $total;
            }
            //税込価格の計算
            $fee = $taxEx * $tax;

            $taxExFee += $taxEx;
            $totalFee += $fee;

            return view('reservation.confirm',compact('room','reservationData','totalFee','taxExFee','user','reservationType'));

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
            return view('reservation.confirm',compact('rooms','reservationData','totalFee','taxExFee','user','reservationType','guests'));
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $reservation = new Reservation();
        $reservation->user_id = Auth::id();
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
        //1部屋の場合
        if($reservationType == "single"){
            $reservationDetail = new ReservationDetail();
            $reservationDetail->reservation_id = $reservation->id;
            $reservationDetail->room_id = $request->input('room_id');
            $reservationDetail->number_of_guests = $request->input('total');
            $reservationDetail->save();
        //2部屋以上の場合
        }else{
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
        return redirect()->route('thanks.index');
    }

    public function thanks()
    {
        return view('reservation.thanks');
    }


    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        // ユーザー情報を表示するため
        $user = Auth::user();
        // ログインしているユーザーと予約の所有者が一致しない場合は、アクセスを拒否する
        if ($reservation->user_id !== $user->id) {
            abort(403, 'アクセスが拒否されました。');
        }
        $reservationId = $reservation->id;
        // 部屋情報を取得する
        $reservation = Reservation::with('user', 'reservation_details.room')->findOrFail($reservationId);
        //税抜価格を表示させるため
        //$taxEx = 1 - $room->tax * 0.01;  //1 - 税率
        //$feeTaxfree = $reservation->reservation_fee * $taxEx;

        return view('reservation.show',compact('reservation','user'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        // ユーザー情報を表示するため
        $user = Auth::user();
        // ログインしているユーザーと予約の所有者が一致しない場合は、アクセスを拒否する
        if ($reservation->user_id !== $user->id) {
            abort(403, 'アクセスが拒否されました。');
        }

        return view('reservation.edit',compact('reservation'));
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
            if($roomNumber >= 2){
                //予約できる部屋を表示
                $availableRooms = Room::whereNotIN('id',$reservedRooms) 
                ->where('status',0)  // 利用可能な部屋のみを対象
                ->get();
            }else{
                //予約できる（人数以上のキャパを持つ）部屋を表示
                $availableRooms = Room::whereNotIN('id',$reservedRooms) 
                ->where('status',0)  // 利用可能な部屋のみを対象
                ->where('capacity','>=',$total)
                ->get();
            }
            //現在予約中の部屋を表示させる
            $reservationId = $reservation->id;
            $reservation = Reservation::with('reservation_details.room')->findOrFail($reservationId);


            //1人1泊の料金の計算
            $totalFee = [];
            $flag1 = false;
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
                //1部屋の時,宿泊人数以上の部屋があればTrue
                if( $room->capacity >= $total && $roomNumber == 1){
                    $flag1 = true;
                }elseif($roomNumber >= 2){
                    $flag1 = true;
                }
            }

            $currentFee = [];
            $flag2 = false;
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
                //1部屋の時,宿泊人数以上の部屋があればTrue
                if( $detail->room->capacity >= $total && $roomNumber == 1){
                    $flag2 = true;
                }elseif($roomNumber >= 2){
                    $flag2 = true;
                }
            }
            //1部屋の時,宿泊人数以上の部屋が現在の部屋にも空き部屋にも存在しない場合エラーを返す。
            if($flag1 == false && $flag2 == false){
                return redirect()->back()->with('error',"ご希望の部屋をご用意できませんでした。");
            }

            return view('reservation.editshowroom', compact('availableRooms','reservation','roomNumber','total','totalFee','dinner','breakfast','currentFee'));
    
    }

    public function editconfirm(Request $request,Reservation $reservation)
    {
        // ユーザー情報を表示するため
        $user = Auth::user();
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
            return view('reservation.editconfirm',compact('room','reservationData','totalFee','taxExFee','user','reservationType','reservation'));
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
            return view('reservation.editconfirm',compact('rooms','reservationData','totalFee','taxExFee','user','reservationType','reservation','guests'));
        }

    }

    //Dependency Injection, DI PaymentServiceクラスのインスタンスを、別のクラスの内部で使用するため
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    /**

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation,ReservationDetail $reservationDetail)
    {
        if($reservation->payment_status == 1){
            try {
                $this->paymentService->refund($reservation->payment_number);
                $message = '返金処理が行われました。再度、マイページより決済を行ってください。';
            } catch (\Exception $e) {
                $message = '返金処理に失敗しました。: ' . $e->getMessage() . ' Reservation cancelled without refund.';
            }
        }else{
            $message = 'ご予約の変更が正しく行われました。';
        }

        $reservation->user_id = Auth::id();
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
            //対象の予約を１度削除し
            ReservationDetail::where('reservation_id',$reservation->id)->delete();
            //対象の予約を１度削除し
            $reservationDetail = new ReservationDetail();
            $reservationDetail->reservation_id = $reservation->id;
            $reservationDetail->room_id = $request->input('room_id');
            $reservationDetail->number_of_guests = $request->input('total');
            $reservationDetail->save();
    //---------2部屋以上の場合---------------------------------------------------------------
        }else{
            //対象の予約を１度削除し
            ReservationDetail::where('reservation_id',$reservation->id)->delete();
            //対象の予約を１度削除し
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

        return redirect()->route('thanks.index')->with('update',$message);
    }
    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Reservation $reservation)
    {

        if($reservation->payment_status == 1){
            try {
                $this->paymentService->refund($reservation->payment_number);
                
                $message = 'ご予約は正常にキャンセルされ、返金処理が行われました。';
            } catch (\Exception $e) {
                
                $message = '返金処理に失敗しました。: ' . $e->getMessage() . ' Reservation cancelled without refund.';
            }
        }else{
            $message = 'ご予約は正常にキャンセルされました。';
        }

        $reservation->delete();
        
        return redirect()->route('mypage.index')->with('delete',$message);
    }
}
