<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function subCategory(){
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    public function brand(){
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function productmodel(){
        return $this->belongsTo(Productmodel::class, 'productmodel_id');
    }
    public function color(){
        return $this->belongsTo(Color::class, 'color_id');
    }
    public function size(){
        return $this->belongsTo(Size::class, 'size_id');
    }
    public function unit(){
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function productimage(){
        return $this->hasMany(ProductDetail::class, 'product_id');
    }
}
