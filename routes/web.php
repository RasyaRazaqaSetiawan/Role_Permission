<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'setting', 'middleware' => ['auth', 'role:Admin']], function () {
    Route::group(['prefix' => 'users', 'middleware' => 'auth'], function () {
        Route::resource('user', UserController::class);
    });
    Route::group(['prefix' => 'permissions'], function () {
        Route::resource('permissions', PermissionController::class);
    });
    Route::group(['prefix' => 'role'], function () {
        Route::resource('role', RoleController::class);
    });
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
