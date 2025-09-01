<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\JobCategoryController;
use App\Http\Controllers\Applicant\AuthController as ApplicantAuthController;
use App\Http\Controllers\Base\AuthController as BaseAuthController;
use App\Http\Controllers\Base\UploadController;
use App\Http\Controllers\Company\AuthController as CompanyAuthController;
use App\Http\Controllers\Company\JobController;
use App\Http\Controllers\Home\CityController as HomeCityController;
use App\Http\Controllers\Home\CompanyController as HomeCompanyController;
use App\Http\Controllers\Home\JobCategoryController as HomeJobCategoryController;
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

// route auth
Route::group(['as' => 'auth.', 'prefix' => 'auth'], function () {
    Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('/logout', action: [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change_password');
    });

    Route::post('/forgot-password', [BaseAuthController::class, 'forgotPassword'])->name('forgot_password');
    Route::post('/reset-password/{token}', [BaseAuthController::class, 'resetPassword'])->name('reset_password');
    
    Route::group(['as' => 'company.', 'prefix' => 'company'], function () {
        Route::post('/login', [CompanyAuthController::class, 'login'])->name('login');
        Route::post('/refresh', [CompanyAuthController::class, 'refresh'])->name('refresh');
        Route::post('/register', [CompanyAuthController::class, 'register'])->name('register');
        Route::post('/logout', [CompanyAuthController::class, 'logout'])->name('logout');
        Route::get('/me', [CompanyAuthController::class, 'me'])->name('me');
        Route::post('/change-password', [CompanyAuthController::class, 'changePassword'])->name('change_password');
    });

    Route::group(['as' => 'applicant.', 'prefix' => 'applicant'], function () {
        Route::post('/login', [ApplicantAuthController::class, 'login'])->name('login');
        Route::post('/refresh', [ApplicantAuthController::class, 'refresh'])->name('refresh');
        Route::post('/register', [ApplicantAuthController::class, 'register'])->name('register');
        Route::post('/logout', [ApplicantAuthController::class, 'logout'])->name('logout');
        Route::get('/me', [ApplicantAuthController::class, 'me'])->name('me');
        Route::post('/change-password', [ApplicantAuthController::class, 'changePassword'])->name('change_password');
    });
});

// route admin
Route::group(
    [
        'as' => 'admin.',
        'prefix' => 'admin',
        'middleware' => ['auth:admin', 'is-admin']
    ],
    function () {
        Route::group(['as' => 'company.', 'prefix' => 'company'], function () {
            Route::get('/', [CompanyController::class, 'list'])->name('list');
            Route::get('/{id}', [CompanyController::class, 'detail'])->name('detail');
            Route::put('/{id}', [CompanyController::class, 'update'])->name('update');
            Route::delete('{id}', [CompanyController::class, 'delete'])->name('delete');
        });

        Route::group(['as' => 'applicant.', 'prefix' => 'applicant'], function () {
            Route::get('/', [ApplicantController::class, 'list'])->name('list');
            Route::get('/{id}', [ApplicantController::class, 'detail'])->name('detail');
            Route::put('/{id}', [ApplicantController::class, 'update'])->name('update');
            Route::delete('{id}', [ApplicantController::class, 'delete'])->name('delete');
        });

        Route::group(['as' => 'master_data.', 'prefix' => 'master-data'], function () {
            Route::group(['as' => 'cities.', 'prefix' => 'cities'], function () {
                Route::get('/', [CityController::class, 'list'])->name('list');
                Route::post('/', [CityController::class, 'store'])->name('store');
                Route::put('/{id}', [CityController::class, 'update'])->name('update');
                Route::delete('{id}', [CityController::class, 'delete'])->name('delete');
            });

            Route::group(['as' => 'job_categories.', 'prefix' => 'job-categories'], function () {
                Route::get('/', [JobCategoryController::class, 'list'])->name('list');
                Route::post('/', [JobCategoryController::class, 'store'])->name('store');
                Route::put('/{id}', [JobCategoryController::class, 'update'])->name('update');
                Route::delete('{id}', [JobCategoryController::class, 'delete'])->name('delete');
            });
        });
    }
);

// route applicant
Route::group(
    [
        'as' => 'applicant.',
        'prefix' => 'applicant',
        'middleware' => ['auth:applicant', 'is-applicant']
    ],
    function () {
        Route::put('/update', [ApplicantAuthController::class, 'update'])->name('update');
    }
);

// route company
Route::group(
    [
        'as' => 'company.',
        'prefix' => 'company',
        'middleware' => ['auth:company', 'is-company']
    ],
    function () {
        Route::put('/update', [CompanyAuthController::class, 'update'])->name('update');

        Route::group(['as' => 'job.', 'prefix' => 'job'], function () {
            Route::get('/', [JobController::class, 'list'])->name('list');
        });
    }
);

// route home
Route::group(
    [
        'as' => 'home.',
        'prefix' => 'home',
    ],
    function () {
        Route::group(['as' => 'masterData.', 'prefix' => 'master-data'], function () {
            Route::get('/cities', [HomeCityController::class, 'list'])->name('cities');
            Route::get('/cities-parent', [HomeCityController::class, 'listParent'])->name('cities_parent');
            Route::get('/job-categories', [HomeJobCategoryController::class, 'list'])->name('job_categories');
            Route::get('/job-categories-parent', [HomeJobCategoryController::class, 'listParent'])->name('job_categories_parent');
        });

        Route::group(['as' => 'company.', 'prefix' => 'company'], function () {
            Route::get('/', [HomeCompanyController::class, 'list'])->name('list');
        });
    }
);

Route::group(
    [
        'as' => 'upload.',
        'prefix' => 'upload',
    ],
    function () {
        Route::post('/image', [UploadController::class, 'uploadImg'])->name('image');
    }
);


