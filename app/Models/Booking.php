<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'booking_date',
        'booking_type',
        'booking_slot',
        'booking_from_time',
        'booking_to_time',
    ];
}
