<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api_customer');
    }

    public function index()
    {
        $carts = Cart::with('product')
            ->where('customer_id', auth()->guard('api_customer')->user()->id)
            ->latest()
            ->get();

        return new CartResource(true, 'List data cart : ' . auth()->guard('api_customer')->user()->name, $carts);
    }

    public function store(Request $request)
    {
        $item = Cart::where('product_id', $request->product_id)
            ->where('customer_id', auth()->guard('api_customer')->user()->id);

        if ($item->count()) {
            //increments
            $item->increment('qty');
            $item = $item->first();

            //sum price
            $price = $request->price * $item->qty;

            //sum weight
            $weight = $request->weight * $item->qty;

            $item->update([
                'price' => $price,
                'weight' => $weight
            ]);
        } else {
            //insert
            Cart::create([
                'product_id' => $request->product_id,
                'customer_id' => auth()->guard('api_customer')->user()->id,
                'qty' => $request->qty,
                'price' => $request->price,
                'weight' => $request->weight,

            ]);

            return new CartResource(true, 'Item berhasil ditambahkan ke cart', $item);
        }
    }

    public function getPrice()
    {
        $totalPrice = Cart::with('product')
            ->where('customer_id', auth()->guard('api_customer')->user()->id)
            ->sum('price');

        return new CartResource(true, 'Total harga cart', $totalPrice);
    }

    public function getWeight()
    {
        $totalWeight = Cart::with('product')
            ->where('customer_id', auth()->guard('api_customer')->user()->id)
            ->sum('weight');

        return new CartResource(true, 'Total berat cart', $totalWeight);
    }

    public function removeCart(Request $request)
    {
        $cart = Cart::with('product')
            ->whereId($request->cart_id)
            ->first();

        $cart->delete();

        return new CartResource(true, 'Item cart berhasil dihapus', null);
    }
}
