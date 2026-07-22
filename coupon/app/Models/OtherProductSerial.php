<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherProductSerial extends Model
{
    use HasFactory;

    // public function otherproduct()
    // {
    //     return $this->belongsTo(OtherProduct::class);
    // }

    public function otherproduct()
    {
        return $this->belongsTo(OtherProduct::class, 'product_id');
    }
}
