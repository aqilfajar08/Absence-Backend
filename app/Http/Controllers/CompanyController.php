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
        $company->update($request->all());
        return redirect()->route('company.show', $company->id)->with('success', 'Company updated successfully');
    }
}