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
            'permission' => 'required', // Reason/Keterangan
            'permit_type' => 'required|in:sakit,izin,cuti,dlk', // Jenis Izin
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
        ]);

        $permission = new Permit;
        $permission->user_id = $request->user()->id;
        $permission->date_permission = now()->format('Y-m-d');
        $permission->reason = $request->permission;
        $permission->permit_type = $request->permit_type;
        $permission->is_approved = 'pending'; // default value

        if($request->hasFile('image'))
        {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->storeAs('permissions', $fileName, 'public');
            $permission->image = $fileName;
        }

        $permission->save();
        return response()->json(['message' => 'Permission created successfully.'], 201);
    }
}