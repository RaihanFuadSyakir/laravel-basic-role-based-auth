<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
class UserController extends Controller
{
    public function index(){
        $users = User::with('roles')->get();
        $result = [];
        foreach($users as $user){
            $result [] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray()
            ];
        }
        return Inertia::render('users/ManageUser', ['users' => $result]);
    }
    public function create(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|email',
            'password' => 'required|min:8|max:100'
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        return redirect()->route('manage_users')
             ->with('success', 'User created successfully');
    }
    public function update(Request $request){
        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|email',
            'change_password' => 'required|boolean',
            'password' => [ Rule::requiredIf($request->boolean('change_password')),'string','min:8','max:100',],
        ]);
        $user = User::findOrFail($validated['id']);
        if ($validated['change_password']) {
            $user->password = Hash::make($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('manage_users')
             ->with('success', 'User updated successfully');
    }
}
