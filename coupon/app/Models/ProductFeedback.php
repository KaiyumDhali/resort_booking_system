<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFeedback extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'product_id',
        'product_serial',
        'customer_email',
        'customer_phone',
        'feedback',
        'feedback_date',
    ];

}
