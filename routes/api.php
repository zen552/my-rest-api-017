<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AnimeMerchController;

Route::prefix('user')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/whoami', function (Request $request) {
    return $request->user();
    })->middleware('auth:api');
});

Route::resource('anime_merch', AnimeMerchController::class, [
    'only'=> [
        'index',
        'show'
    ]
]);

Route::resource('anime_merch', AnimeMerchController::class, [
    'except' => [
        'index',
        'show'
    ]
])->middleware(['auth:api']);