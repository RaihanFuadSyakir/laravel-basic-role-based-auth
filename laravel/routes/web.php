<?php

use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth','verified','permission:view_users'])->group(function(){
    Route::get('manage_users', [UserController::class,'index']);
});
Route::middleware(['auth','verified','permission:view_permissions'])->group(function(){
    Route::get('manage_permissions', [PermissionController::class,'index']);
});
Route::middleware(['auth','verified','permission:view_roles'])->group(function(){
    Route::get('manage_roles', [RoleController::class,'index']);
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
