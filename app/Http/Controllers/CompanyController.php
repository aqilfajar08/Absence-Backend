<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function show($id) {
        $company = Company::find($id);
        return view('pages.company.show', compact('company'));
    }
    
    public function edit($id) {
        $company = Company::find($id);
        return view('pages.company.edit', compact('company'));
    }

    public function update(Request $request, $id) {
        $company = Company::find($id);
        $data = $request->all();
        
        // Convert radius_m to radius_km
        if ($request->filled('radius_m')) {
            $data['radius_km'] = ((float) $request->input('radius_m')) / 1000.0;
        }
        unset($data['radius_m']);
        
        // Convert deduction percentages (from input) to received percentages (for database)
        // User inputs: 25% deduction → Database stores: 75% received
        if (isset($data['gph_late_1_percent'])) {
            $data['gph_late_1_percent'] = 100 - (int)$data['gph_late_1_percent'];
        }
        if (isset($data['gph_late_2_percent'])) {
            $data['gph_late_2_percent'] = 100 - (int)$data['gph_late_2_percent'];
        }
        if (isset($data['gph_late_3_percent'])) {
            $data['gph_late_3_percent'] = 100 - (int)$data['gph_late_3_percent'];
        }
        if (isset($data['gph_late_4_percent'])) {
            $data['gph_late_4_percent'] = 100 - (int)$data['gph_late_4_percent'];
        }
        
        $company->update($data);
        return redirect()->route('company.edit', $company->id)->with('success', 'Pengaturan berhasil disimpan.');
    }
}