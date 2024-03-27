<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Refund;

class PaymentService
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('stripe.stripe_secret_key'));
    }

    public function refund($chargeId)
    {
        try {
            $refund = Refund::create(['charge' => $chargeId]);
            return $refund;
        } catch (\Exception $e) {
            // エラーハンドリング: 実際のアプリケーションではエラーログを記録する等の処理が必要
            throw $e;
        }
    }
}
