<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function show(Request $request) {
        $company = Company::first();
        return response()->json([
            'company' => $company,
            'radius_m' => $company ? (float)$company->radius_km * 1000 : null,
        ]);
    }
}
