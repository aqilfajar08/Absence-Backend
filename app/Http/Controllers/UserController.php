<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\post;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        return view('pages.users.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'position' => 'required',
            'department' => 'required',
            'password' => 'required',
            'email' => 'required|email',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'department' => $request->department,
            'password' => Hash::make($request->password),
        ]);
        return redirect()->route('user.index')->with('success', 'User created successfully');
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'position' => 'required',
            'department' => 'required',
            'email' => 'required|email',
        ]);
        
        $data = $request->except('password');

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'User updated successfully');
    }

    public function delete($id) {
        $user = User::findOrFail($id);
        return view('pages.users.delete', compact('user'));
    }

    public function destroy(User $user) {
        $user->delete();

        return redirect()->route('user.index')->with('success', 'User deleted successfully');
    } 
}
