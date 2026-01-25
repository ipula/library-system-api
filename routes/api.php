<?php

use App\Interfaces\Http\Controllers\Auth\AuthController;
use App\Interfaces\Http\Controllers\Book\BookController;
use App\Interfaces\Http\Controllers\BookRental\BookRentalController;
use App\Interfaces\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:login')->post('login', [AuthController::class, 'login']);
    Route::post('forgotPassword', [AuthController::class, 'forgotPassword']);
    Route::get('/resetPassword/{token}', function ($token) {
        return response()->json([
            'token' => $token,
        ]);
    })->name('password.reset');
    Route::post('resetPassword', [AuthController::class, 'resetPassword']);
    Route::apiResource('users', UserController::class)->only(['store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::apiResource('users', UserController::class)->except(['store']);;
        Route::apiResource('books', BookController::class);
        Route::post('rentBooks', [BookRentalController::class,'rentBooks'])->name('books.rentBooks');
        Route::get('getRentalReadingProgress/{rentalId}', [BookRentalController::class,'rentalReadingProgress'])->name('books.getRentBooks');
        Route::patch('rentExtend/{rentalId}', [BookRentalController::class,'extendRental'])->name('books.extendRental');
        Route::patch('updateRentProgress/{rentalId}', [BookRentalController::class,'updateProgress'])->name('books.updateProgress');
        Route::patch('rentFinish/{rentalId}', [BookRentalController::class,'finishRental'])->name('books.finishRental');
    });

});
