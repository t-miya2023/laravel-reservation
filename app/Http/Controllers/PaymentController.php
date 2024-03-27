<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function create($reservationId)
    {
        // ユーザー情報を表示するため
        $user = Auth::user();
        $reservation = Reservation::findOrFail($reservationId);
        // ログインしているユーザーと予約の所有者が一致しない場合は、アクセスを拒否する
        if ($reservation->user_id !== $user->id) {
            abort(403, 'アクセスが拒否されました。');
        }

        return view('payment.create',compact('reservation'));
    }

    public function store(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('stripe.stripe_secret_key'));
        $reservation = Reservation::findOrFail($request->reservation_id);

        try {
            $charge = \Stripe\Charge::create([
                'source' => $request->stripeToken,
                'amount' => $reservation->reservation_fee,
                'currency' => 'jpy',
            ]);

            $reservation->payment_status = 1; //支払い済みに変更
            $reservation->payment_number = $charge->id; //支払い番号を取得
            $card = $charge->source;
            $reservation->creditcard_company = $card->brand; //カードブランドを取得
            $reservation->save();
            

        } catch (Exception $e) {
            return back()->with('flash_alert', '決済に失敗しました！('. $e->getMessage() . ')');
        }
        return redirect()->route('mypage.index')->with('status', '決済が完了しました！');
    }

    
}
