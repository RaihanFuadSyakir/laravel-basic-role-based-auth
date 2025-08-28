<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $nameFilter = $request->query('name');
        $perPage = $request->input('per_page', 10);
        $permissions = Permission::query();
        if($nameFilter){
            $permissions = $permissions->where('name','like',"%{$nameFilter}%");
        }
        $permissions = $permissions->paginate($perPage)
            ->appends($request->all());
        $permissions->getCollection()->transform(function ($permission) {
            return [
                'created_at' => $permission->created_at->toDateTimeString(),
                'name' => $permission->name,
            ];
        });
        $toSend = [
            'data' => [
                'permissions' => $permissions->items()
            ],
            'pagination' => [
                'total' => $permissions->total(),
                'currentPage' => $permissions->currentPage(),
                'lastPage' => $permissions->lastPage()
            ]
        ];
        return Inertia::render('permissions/ManagePermission',$toSend);
    }
}
