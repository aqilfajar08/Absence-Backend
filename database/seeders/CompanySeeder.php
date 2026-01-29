<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::updateOrCreate(
            ['email' => 'marketing@kasaugrup.com'],
            [
                'name' => 'PT. Kasau Sinar Samudera',
                'address' => 'Komp. Balikpapan Baru B5, Damai, Kecamatan Balikpapan Selatan, Kota Balikpapan, Kalimantan Timur 76133',
                'latitude' => '-1.2466006096285236',
                'longitude' => '116.85965676089455',
                'radius_km' => '1',
                'time_in' => '08:00',
                'time_out' => '17:00',
            ]
        );
    }
}
