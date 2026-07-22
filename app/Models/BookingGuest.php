<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingGuest extends Model
{
    protected $table = 'booking_guests';

    protected $fillable = [
        'booking_no',
        'name',
        'nid',
        'mobile',
        'address',
        'relation',
        'customer_status'
    ];
}
