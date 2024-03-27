<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Reservation;
use App\Models\ReservationDetail;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;


class MypageController extends Controller
{

    public function index()
    {   
        //ログインしているユーザーの情報を取得
        $user = Auth::user();

        $currentDate = Carbon::now()->toDateString(); //現在の日付を取得
        //ログインしているユーザーの予約情報を取得
        $reservations = Reservation::with('user', 'reservation_details.room')
                                    ->where('user_id',$user->id)
                                    ->where('checkin_date', '>=', $currentDate) //過去の日付の情報は非表示
                                    ->orderBy('checkin_date') //チェックイン順に並び替え
                                    ->get();
        //日付のフォーマットを変更する
        foreach($reservations as $reservation){
            //チェックイン
            $checkInDate = Carbon::parse($reservation->checkin_date)->isoFormat('YYYY年MM月DD日');
            //フォーマットしたものを再度セットする
            $reservation->checkin_date = $checkInDate;
            //チェックアウト
            $checkOutDate = Carbon::parse($reservation->checkout_date)->isoFormat('YYYY年MM月DD日');
            //フォーマットしたものを再度セットする
            $reservation->checkout_date = $checkOutDate;
        }

        return view('mypage.index',compact('user','reservations'));
    }

    public function edit()
    {
        //ログインしているユーザーの情報を取得
        $user = Auth::user();

        return view('mypage.edit',compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|max:255',
            'email' => ['required','email',Rule::unique('users')->ignore($user->id)],
            'tel' => 'required|numeric',
            'post_code' => 'required|size:7',
            'address' => 'required',
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->tel = $request->input('tel');
        $user->post_code = $request->input('post_code');
        $user->address = $request->input('address');

        $user->save();

        return redirect()->route('mypage.index')->with('update','ユーザー情報は正しく変更されました。');

    }
    
    public function change_password()
    {
        //ログインしているユーザーの情報を取得
        $user = Auth::user();

        return view('mypage.change_password',compact('user'));
    }

    public function update_password(Request $request,$id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|min:8'
        ]);
        $new_password = $request->password;
        $old_password = $request->currentpassword;
        $confirm_password = $request->confirmpassword;

        if(!(Hash::check($old_password,$user->password))){
            return redirect()->back()->with('caution','現在のパスワードが間違っています。');
        }else{
            if(Hash::check($new_password,$user->password)){
                return redirect()->back()->with('caution','新しいパスワードが現在のパスワードと同じです。違うパスワードを設定してください。');
        }else{
            if($new_password != $confirm_password){
                return redirect()->back()->with('caution','新しいパスワードと確認用パスワードが一致しません。');
            }else{
                $user->password = Hash::make($request['password']);
                $user->save();
                return redirect()->route('mypage.index')->with('update','パスワードは正しく変更されました。');
                }
            }
        }
    }
}
