<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalServicePrice extends Model
{
    use HasFactory;
  

protected $fillable = [
    'additional_service_id',
    'min_person',
    'max_person',
    'price',
    'status',
];
public function service()
{
    return $this->belongsTo(AdditionalService::class, 'additional_service_id');
}
}
