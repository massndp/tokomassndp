<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\RajaongkirResource;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaongkirController extends Controller
{
    public function getProvinces()
    {
        $provinces = Province::all();

        return new ProductResource(true, 'List data province', $provinces);
    }

    public function getCities(Request $request)
    {
        //get province
        $province = Province::where('province_id', $request->province_id)->first();

        //get cities
        $city = City::where('province_id', $request->province_id)->get();

        return new RajaongkirResource(true, 'List data city by province : ' . $province->name . '', $city);
    }

    public function checkOngkir(Request $request)
    {
        //Fetch REST API
        $response = Http::withHeaders([
            'key' => config('services.rajaongkir.key')
        ])->post('https://api.rajaongkir.com/starter/cost', [

            //send data
            'origin' => 283, //Id kota Metro,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier
        ]);

        //return API RESOURCE
        return new RajaongkirResource(true, 'List data biaya ongkos kirim: ' . $request->courier . '', $response['rajaongkir']['results'][0]['costs']);
    }
}
