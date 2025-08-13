<?php

use App\Http\Controllers\Manage\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware(['auth','verified','view_users'])->group(function(){
    Route::get('manage_users', [UserController::class,'index']);
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
