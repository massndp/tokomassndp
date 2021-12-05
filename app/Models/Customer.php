<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;


    protected $fillable = [
        'name', 'email', 'email_verified_at', 'password', 'remember_token'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getCreatedAtAttribute($date)
    {
        $value = Carbon::parse($date);
        $parse = $value->locale('id');
        return $parse->translatedFormat('l, d F Y');
    }
}
