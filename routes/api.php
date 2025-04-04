<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Applicant\AuthController as ApplicantAuthController;
use App\Http\Controllers\Company\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */


 Route::group(['as' => 'auth.', 'prefix' => 'auth'], function () {
    Route::group(['as' => 'company.', 'prefix' => 'company'], function() {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
    });

    Route::group(['as' => 'applicant.', 'prefix' => 'applicant'], function() {
        Route::post('/login', [ApplicantAuthController::class, 'login'])->name('login');
        Route::post('/logout', [ApplicantAuthController::class, 'logout'])->name('logout');
        Route::get('/me', [ApplicantAuthController::class, 'me'])->name('me');
    });

    Route::group(['as' => 'admin.', 'prefix' => 'admin'], function() {
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AdminAuthController::class, 'me'])->name('me');
    });
});
