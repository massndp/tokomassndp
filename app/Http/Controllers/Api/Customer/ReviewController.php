<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        //check already for review

        $check = Review::where('order_id', $request->order_id)->where('product_id', $request->product_id)->first();

        if ($check) {
            return response()->json($check, 409);
        }

        $review = Review::create([
            'rating' => $request->rating,
            'review' => $request->review,
            'product_id' => $request->product_id,
            'order_id' => $request->order_id,
            'customer_id' => $request->customer_id
        ]);

        if ($review) {
            return new ReviewResource(true, 'Data review berhasil ditambahkan', $review);
        }

        return new ReviewResource(false, 'Data review gagal ditambahkan', null);
    }
}
