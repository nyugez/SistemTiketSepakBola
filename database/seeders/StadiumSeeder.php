<?php

namespace Database\Seeders;

use App\Models\Stadium;
use Illuminate\Database\Seeder;

class StadiumSeeder extends Seeder
{
    public function run(): void
    {
        $stadiums = [
            ['name' => 'Gelora Bung Karno',       'city' => 'Jakarta',   'address' => 'Jl. Pintu Satu Senayan, Jakarta Pusat',           'capacity' => 77193],
            ['name' => 'Gelora Bung Tomo',         'city' => 'Surabaya',  'address' => 'Jl. Gadung No.1, Sawunggaling, Wonokromo',         'capacity' => 45000],
            ['name' => 'Stadion Utama Palaran',    'city' => 'Samarinda', 'address' => 'Jl. Poros Samarinda-Bontang, Palaran',             'capacity' => 40000],
            ['name' => 'Stadion Manahan',          'city' => 'Solo',      'address' => 'Jl. Adi Sucipto No.1, Manahan, Banjarsari',        'capacity' => 25000],
            ['name' => 'Pakansari Stadium',        'city' => 'Bogor',     'address' => 'Jl. Pakansari, Cibinong, Kabupaten Bogor',         'capacity' => 30000],
            ['name' => 'Wibawa Mukti Stadium',     'city' => 'Bekasi',    'address' => 'Jl. Wibawa Mukti II, Jatiasih, Kota Bekasi',       'capacity' => 25000],
        ];

        foreach ($stadiums as $data) {
            Stadium::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
