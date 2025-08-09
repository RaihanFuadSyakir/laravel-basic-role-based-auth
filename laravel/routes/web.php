<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'permission:view_dashboard']);

Route::get('/manage-users', fn() => view('manage-users'))
    ->middleware(['auth', 'permission:manage_users']);
