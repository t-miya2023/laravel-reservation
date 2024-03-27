<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation_details()
    {
        return $this->hasMany(ReservationDetail::class);
    }
    
    public function rooms()
    {
        return $this->hasManyThrough(Room::class,ReservationDetail::class);
    }

    protected $fillable = [
        'reservation_date',
        'checkin_date',
        'checkin_time',
        'checkout_date',
        'total',
        'number_of_men',
        'number_of_women',
        'dinner',
        'breakfast',
        'payment_info',
        'reservation_fee',
        'remarks_column',
        'payment_status',
        'payment_number',
        'creditcard_company',
    ];

    protected $dates = [
        'reservation_date',
        'checkin_date',
        'checkin_time',
        'checkout_date',
        'total',
        'number_of_men',
        'number_of_women',
        'dinner',
        'breakfast',
        'payment_info',
        'reservation_fee',
        'remarks_column',
        'payment_status',
        'payment_number',
        'creditcard_company',
    ];
}
