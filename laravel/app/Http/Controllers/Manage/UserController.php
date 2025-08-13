<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return Inertia::render('users/ManageUser', ['users' => $users]);
    }
}
