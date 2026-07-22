<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSerial extends Model {

    use HasFactory;

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function productdetails() {
        return $this->hasMany(ProductDetail::class, 'product_id');
    }

//    public function category() {
//        return $this->belongsTo(Category::class);
//    }
//
//    public function subcategory() {
//        return $this->belongsTo(Subcategory::class);
//    }
}
