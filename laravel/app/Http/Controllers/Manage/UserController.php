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
    public function index(Request $request){
        $perPage = $request->input('per_page', 10);
        $users = User::with('roles')->paginate($perPage)->appends($request->all());
        $result = [];
        foreach($users as $user){
            $result [] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray()
            ];
        }
        $toSend = [
            'users' => $result,
            'pagination' => [
                'total' => $users->total(),
                'currentPage' => $users->currentPage(),
                'lastPage' => $users->lastPage()
            ]
        ];
        return Inertia::render('users/ManageUser', $toSend);
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
