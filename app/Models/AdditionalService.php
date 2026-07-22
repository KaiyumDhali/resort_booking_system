<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalService extends Model
{
    use HasFactory;

    protected $fillable = [
    'title',
    'description',
    'price',
    'status',
    'is_global',
    'editable_status',
    'is_backend',
    'is_frontend',
];
public function spots()
{
    return $this->belongsToMany(Spot::class, 'additional_service_spot');
}
public function prices()
{
    return $this->hasMany(AdditionalServicePrice::class);
}
}
