<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [

        'behaviour_rating',
        'behaviour_note',

        'facility_rating',
        'facility_note',

        'service_rating',
        'service_note',

        'visit_again',
        'visit_reason',

        'price_rating',
        'price_note',
        
        'recommend',
        'recommend_reason',
        
        

        'note',

        'name',
        'email',
        'mobile',
        'address'
    ];
}