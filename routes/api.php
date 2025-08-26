<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TodoController;

Route::apiResource('todos', App\Http\Controllers\API\TodoController::class);
// Route::apiResource('todos', TodoController::class);
// Route::patch('todos/{id}', [TodoController::class, 'partialUpdate'])->name('todos.partialUpdate');
