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
        $roles = Role::with('permissions:name');
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
                'name' => $role->name,
                'permissions' => $role->permissions()->pluck('name')->toArray(),
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
    }
    public function  update (Request $request){
    }
    public function  delete (Request $request){
    }
}

