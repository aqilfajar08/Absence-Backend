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
        if ($request->filled('radius_m')) {
            $data['radius_km'] = ((float) $request->input('radius_m')) / 1000.0;
        }
        unset($data['radius_m']);
        $company->update($data);
        return redirect()->route('company.edit', $company->id)->with('success', 'Pengaturan berhasil disimpan.');
    }
}