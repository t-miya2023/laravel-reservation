<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'detail',
        'price',
        'capacity',
        'bed_size',
        'smorking',
        'facility',
        'amenities',
        'img',
        'status',
    ];

    public function reservation_details()
    {
        return $this->hasMany(ReservationDetail::class);
    }
}
