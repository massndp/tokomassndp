<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Fetch Rest API
        $response = Http::withHeaders([
            'key' => config('services.rajaongkir.key'),
        ])->get('https://rajaongkir.com/starter/province');

        //looping data from Rest API
        foreach ($response['rajaongkir']['results'] as $province) {
            //insert to province table
            Province::create([
                'province_id' => $province['province_id'],
                'name' => $province['province']
            ]);
        }
    }
}
