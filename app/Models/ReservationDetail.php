<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationDetail extends Model
{
    use HasFactory;

    public function reservation()
    {
        return $this->belongsTo(Resevation::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    protected $fillable = [
        'number_of_guests',
    ];
}
