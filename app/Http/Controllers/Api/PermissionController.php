<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function store(Request $request) 
    {
        $request->validate([
            'permission' => 'required',
        ]);

        $permission = new Permit;
        $permission->user = $request->user()->id;
        $permission->date_permission = now()->format('Y-m-d');
        $permission->reason = $request->permission;
        $permission->is_approved = false;

        if($request->hasFile('image'))
        {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->storeAs('permissions', $fileName, 'public');
            $permission->image = $fileName;
        }

        $permission->save();
    }
}
