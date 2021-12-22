<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'user_id', 'title', 'image', 'slug', 'description', 'weight', 'price', 'stock', 'discount'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageAttribute($image)
    {
        return asset('storage/products/' . $image);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getReviewsAvgRatingAttribute($value)
    {
        return $value ? substr($value, 0, 3) : 0;
    }
}
