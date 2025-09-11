<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
class UserController extends Controller
{
    public function index(Request $request){
        $perPage = $request->input('per_page', 10);
        $nameFilter = $request->query('name');
        $emailFilter = $request->query('email');
        $rolesFilter = $request->query('roles');
        $roles = Role::where('id','>',1)->pluck('name')->toArray();
        $users = User::with('roles')
            ->where('id','!=',1);
        if($nameFilter){
            $users = $users->where('name','like',"%{$nameFilter}%");
        }
        if($emailFilter){
            $users = $users->where('email','like',"%{$emailFilter}%");
        }
        if ($rolesFilter) {
            // Split the comma-separated string into an array
            $rolesArray = explode(',', $rolesFilter);

            // Filter users by roles
            $users = $users->whereHas('roles', function ($query) use ($rolesArray) {
                $query->whereIn('name', $rolesArray);
            });
        }
        $users =  $users->paginate($perPage)
            ->appends($request->all());
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
            'data' => [
                'users' => $result,
                'roles' => $roles,
            ],
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:100',
            'roles' => 'required|array|min:1',
            'roles.*' => 'string'
        ]);

        // Hash the password
        $validated['password'] = Hash::make($validated['password']);

        // Create the user
        $user = User::create($validated);

        // Get role IDs from names
        $roleIds = Role::whereIn('name', $validated['roles'])->pluck('id')->toArray();

        // Attach roles to user
        $user->roles()->attach($roleIds);

        return redirect()->route('users.index')
             ->with('success', 'User created successfully');
    }
    public function update(Request $request,$id){
        $request->merge(['id' => $id]);
        $validated = $request->validate([
            'id' => 'required|integer|min:2|exists:users,id',
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|email',
            'change_password' => 'required|boolean',
            'password' => 'exclude_unless:change_password,true|string|min:8|max:100',
            'roles' => 'required|array|min:1',
            'roles.*' => 'string'
        ]);
        $user = User::findOrFail($validated['id']);
        if ($validated['change_password']) {
            $validated['password'] = Hash::make($validated['password']);
        }
        // Get role IDs from names
        $roleIds = Role::whereIn('name', $validated['roles'])->pluck('id')->toArray();
        $user->update($validated);
        // Attach roles to user
        $user->roles()->sync($roleIds);
        return redirect()->route('users.index')
             ->with('success', 'User updated successfully');
    }
    public function delete(Request $request, $id)
    {

        $request->merge(['id' => $id]);
        $validated = $request->validate([
            'id' => 'required|integer|min:2|exists:users,id',
        ]);
        // Find the user, or fail with 404
        $user = User::findOrFail($id);

        // Soft delete
        $user->delete();

        // Redirect back with success message
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
