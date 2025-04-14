<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Permit;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index() {
        $permissions = Permit::with('user')
        ->orderBy('id', 'desc')
        ->paginate(10);

        return view('pages.permissions.index', compact('permissions'));
    }
    public function show(Request $request, $id) {
        $permission = Permit::with('user')->find($id);
        return view('pages.permissions.show', compact('permission'));
    }

    public function edit(Request $request, $id) {
        $permission = Permit::find($id);
        return view('pages.permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id) {
        $permission = Permit::find($id);
        $permission->update($request->all());
        return redirect()->route('permission.index', $permission->id)->with('success', 'Permission updated successfully');
    }
}
