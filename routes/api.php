<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Candidate\AuthController as CandidateAuthController;
use App\Http\Controllers\Employer\AuthController;
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
    Route::group(['as' => 'employer.', 'prefix' => 'employer'], function() {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
    });

    Route::group(['as' => 'candidate.', 'prefix' => 'candidate'], function() {
        Route::post('/login', [CandidateAuthController::class, 'login'])->name('login');
        Route::post('/logout', [CandidateAuthController::class, 'logout'])->name('logout');
        Route::get('/me', [CandidateAuthController::class, 'me'])->name('me');
    });

    Route::group(['as' => 'admin.', 'prefix' => 'admin'], function() {
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AdminAuthController::class, 'me'])->name('me');
    });
});
