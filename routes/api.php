<?php

namespace routes;

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyAppSignature;

Route::middleware([
    VerifyAppSignature::class,
])->group(function () {
    Route::get('/secure-data', function () {
        return response()->json(['message' => 'Access granted']);
    });
});

Route::middleware([])->group(function () {
    Route::get('/secure-data', function () {
        return response()->json(['message' => 'Access granted']);
    });
});


Route::middleware([
    VerifyAppSignature::class,
])->prefix('v1s')->group(function () {
    // user
    Route::prefix('user')->group(function () {
        Route::post('/login', [UserController::class, 'index']);
        Route::post('/register', [UserController::class, 'register']);
    });

    // project
    Route::prefix('project')->group(function () {
        Route::get('/list', [ProjectController::class, 'index']);
        Route::get('{project}/statuses', [StatusController::class, 'index']);
        Route::get('{project}/tasks', [TaskController::class, 'index']);
        Route::post('{project}/tasks', [TaskController::class, 'store']);
    });

    Route::patch('/tasks/{task}', [TaskController::class, 'update']);
    Route::patch('/statuses/{status}', [StatusController::class, 'update']);
});
