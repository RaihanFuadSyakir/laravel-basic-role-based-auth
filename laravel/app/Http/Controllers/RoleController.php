<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions:name')
            ->get();
        $result = [];
        foreach($roles as $role){
            $result[] = [
                'name' => $role->name,
                'permissions' => $role->permissions()->pluck('name')->toArray(),
            ];
        };
        return Inertia::render('roles/ManageRole',
        [
            'roles' => $result
        ]);
    }
}
