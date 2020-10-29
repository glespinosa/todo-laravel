<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return auth()->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/todos', [TodoController::class, 'index']);
    Route::post('/todos', [TodoController::class, 'store']);
    Route::patch('/todos/{todo}', [TodoController::class, 'update']);
    Route::delete('/todos/{id}', [TodoController::class, 'destroy']);
    Route::patch('/todosCheckAll', [TodoController::class, 'updateAll']);
    Route::delete('/todosDeleteCompleted', [TodoController::class, 'destroyCompleted']);
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
