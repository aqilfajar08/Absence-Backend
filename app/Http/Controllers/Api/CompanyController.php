<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function show(Request $request) {
        $company = Company::find(1);
        return response()->json([
            'company' => $company,
            'radius_m' => $company ? $company->radius_km * 1000 : null,
        ]);
    }
}
