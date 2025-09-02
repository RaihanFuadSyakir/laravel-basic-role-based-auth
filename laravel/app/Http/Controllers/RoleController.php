<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
class RoleController extends Controller
{
    public function index(Request $request)
    {
        $nameFilter = $request->query('name');
        $permissionsFilter = $request->query('permission');
        $perPage = $request->input('per_page', 10);
        $roles = Role::where("id","!=",1)->with('permissions:name');
        if($nameFilter){
            $roles = $roles->where('name','like',"%{$nameFilter}%");
        }
        if ($permissionsFilter) {
            // Split the comma-separated string into an array
            $permissionsArray = explode(',', $permissionsFilter);

            // Filter users by roles
            $roles = $roles->whereHas('permissions', function ($query) use ($permissionsArray) {
                $query->whereIn('name', $permissionsArray);
            });
        }
        $roles = $roles->paginate($perPage)
            ->appends($request->all());
        $roles->getCollection()->transform(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions()->pluck('name')->toArray(),
                'level' => $role->level
            ];
        });
        $permissions = Permission::all()->pluck('name')->toArray();
        $toSend = [
            'data' => [
                'roles' => $roles->items(),
                'permissions' => $permissions
            ],
            'pagination' => [
                'total' => $roles->total(),
                'currentPage' => $roles->currentPage(),
                'lastPage' => $roles->lastPage()
            ]
        ];
        return Inertia::render('roles/ManageRole',$toSend);
    }
    public function  create (Request $request){
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:50',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|distinct', // each item must be string & unique
            'level' => 'integer|min:1|max:10'
        ]);
        // Create the role
        $role = Role::create([
            'name' => $validated['name'],
            'level' => $validated['level']
        ]);

        // Attach permissions (assuming permissions are identified by 'name')
        $permissionIds = Permission::whereIn('name', $validated['permissions'])->pluck('id');

        $role->permissions()->attach($permissionIds);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully.');
    }
    public function  update (Request $request,$id){
        $request->merge(['id' => $id]);
        $validated = $request->validate([
            'id' => 'required|integer|min:2|exists:roles,id',
            'name' => 'required|string|min:2|max:50',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|distinct', // each item must be string & unique
            'level' => 'required|integer|min:1|max:10'
        ]);
        $role = Role::findOrFail($validated['id']);
        $role->update([
            'name' => $validated['name'],
            'level' => $validated['level']
        ]);
        $permissionIds = Permission::whereIn('name', $validated['permissions'])->pluck('id');
        // detach missing ones and attach new ones
        $role->permissions()->sync($permissionIds);
        return redirect()->route('roles.index')
             ->with('success', 'Role updated successfully');
    }
    public function delete(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        // Validate ID directly (no need to merge into request)
        request()->validate([
            'id' => 'required|integer|min:2|exists:roles,id',
        ]);

        // Find the role or fail
        $role = Role::findOrFail($id);
        // Optionally: prevent deleting special roles (e.g., super-admin)
        if ($role->id === 1) {
            return redirect()->route('roles.index')
                ->with('error', 'The super admin role cannot be deleted.');
        }
        $permissionIds = $role->permissions()->pluck('id')->toArray();
        $role->permissions()->detach($permissionIds);
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}

