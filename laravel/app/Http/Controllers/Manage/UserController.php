<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
}
