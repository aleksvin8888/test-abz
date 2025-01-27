<?php

use App\Http\Controllers\PositionController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/token', [TokenController::class, 'create']);
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'create'])->middleware('validate.token');
Route::get('/users/{id}', [UserController::class, 'show']);
Route::get('/positions', [PositionController::class, 'index']);
