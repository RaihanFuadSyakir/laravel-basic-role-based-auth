<?php

use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');
Route::middleware(['auth','verified'])->group(function(){
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('manage_users', [UserController::class,'index'])
            ->name('manage_users')
            ->middleware('permission:view_users');
    Route::put('users/{id}', [UserController::class,'update'])
            ->middleware('permission:edit_users');
    Route::post('users', [UserController::class,'create'])
            ->middleware('permission:edit_users');
    Route::delete('users/{id}', [UserController::class,'delete'])
            ->middleware('permission:edit_users');
    Route::get('manage_permissions', [PermissionController::class,'index'])
            ->middleware('permission:view_permissions');
    Route::get('manage_roles', [RoleController::class,'index'])
            ->middleware('permission:view_roles');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
