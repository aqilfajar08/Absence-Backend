<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class UpdateCompanyLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();
        
        if (!$company) {
            $company = new Company();
            $company->name = 'PT. KASAU';
            $company->email = 'admin@kasau.com';
        }

        // Update location to PT. Kasau Sinar Samudera
        // https://maps.app.goo.gl/ttSWygnJLp6EvNYZ6
        $company->latitude = '-1.2466024';
        $company->longitude = '116.8597321';
        $company->radius_km = '1'; // 1 km radius
        $company->time_in = '08:00';
        $company->time_out = '17:00';
        
        $company->save();
        
        $this->command->info('Company location updated to: ' . $company->latitude . ', ' . $company->longitude);
    }
}
