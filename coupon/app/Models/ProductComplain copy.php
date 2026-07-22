<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Contact;
use Mail;
use App\Mail\ContactMail;

class ProductComplain extends Model {

    use HasFactory;

    public function product_registration() {
        return $this->belongsTo(ProductRegistration::class, 'product_reg_id');
    }

    public $fillable = ['product_reg_id', 'product_id', 'product_serial', 'complain', 'image_path', 'complain_date', 'status'];

    public static function boot() {

        parent::boot();

        static::created(function ($item) {

//            $adminEmail = "nrbtelecom19@gmail.com";
            $adminEmail = "saffronjusalltd@gmail.com";
            Mail::to($adminEmail)->send(new ContactMail($item));
        });
    }

}
