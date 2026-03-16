<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobCategoryController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Applicant\ApplyController;
use App\Http\Controllers\Applicant\AuthController as ApplicantAuthController;
use App\Http\Controllers\Applicant\FileUploadController;
use App\Http\Controllers\Base\UploadController;
use App\Http\Controllers\Company\AuthController as CompanyAuthController;
use App\Http\Controllers\Company\JobApplicationController;
use App\Http\Controllers\Company\JobController;
use App\Http\Controllers\Home\CityController as HomeCityController;
use App\Http\Controllers\Home\CompanyController as HomeCompanyController;
use App\Http\Controllers\Home\JobCategoryController as HomeJobCategoryController;
use App\Http\Controllers\Home\JobController as HomeJobController;
use App\Http\Controllers\Auth\UnifiedAuthController;
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
    // public routes
    Route::post('/login', [UnifiedAuthController::class, 'login'])->name('login');
    Route::post('/refresh', [UnifiedAuthController::class, 'refresh'])->name('refresh');

    // protected routes
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/me', [UnifiedAuthController::class, 'me'])->name('me');
        Route::post('/change-password', [UnifiedAuthController::class, 'changePassword'])->name('change_password');
        Route::post('/logout', [UnifiedAuthController::class, 'logout'])->name('logout');
    });

    Route::post('/forgot-password', [UnifiedAuthController::class, 'forgotPassword'])->name('forgot_password');
    Route::post('/reset-password/{token}', [UnifiedAuthController::class, 'resetPassword'])->name('reset_password');

    Route::group(['as' => 'company.', 'prefix' => 'company'], function () {
        Route::post('/register', [CompanyAuthController::class, 'register'])->name('register');
    });

    Route::group(['as' => 'applicant.', 'prefix' => 'applicant'], function () {
        Route::post('/register', [ApplicantAuthController::class, 'register'])->name('register');
    });
});

// route admin
Route::group(
    [
        'as' => 'admin.',
        'prefix' => 'admin',
        'middleware' => ['auth:api', 'role:admin']
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'list'])->name('dashboard.list');

        Route::group(['as' => 'company.', 'prefix' => 'company'], function () {
            Route::get('/', [CompanyController::class, 'list'])->name('list');
            Route::get('/get-select', [CompanyController::class, 'listCompany'])->name('get_select');
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

        Route::group(['as' => 'job.', 'prefix' => 'job'], function () {
            Route::get('/', [AdminJobController::class, 'list'])->name('list');
            Route::delete('{id}', [AdminJobController::class, 'delete'])->name('delete');
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
        'middleware' => ['auth:api', 'role:applicant']
    ],
    function () {
        Route::put('/update', [ApplicantAuthController::class, 'update'])->name('update');

        Route::group(['as' => 'file-upload.', 'prefix' => 'file-upload'], function () {
            Route::get('/', [FileUploadController::class, 'list'])->name('list');
            Route::put('/upsert', [FileUploadController::class, 'upsert'])->name('upsert');
        });

        Route::group(['as' => 'applied.', 'prefix' => 'applied'], function () {
            Route::get('/', [ApplyController::class, 'list'])->name('list');
        });
    }
);

// route company
Route::group(
    [
        'as' => 'company.',
        'prefix' => 'company',
        'middleware' => ['auth:api', 'role:company']
    ],
    function () {
        Route::put('/update', [CompanyAuthController::class, 'update'])->name('update');

        Route::group(['as' => 'job.', 'prefix' => 'job'], function () {
            Route::get('/', [JobController::class, 'list'])->name('list');
            Route::get('/{id}', [JobController::class, 'detail'])->name('detail');
            Route::put('/{id}', [JobController::class, 'update'])->name('update');
            Route::post('/', [JobController::class, 'store'])->name('store');
            Route::delete('{id}', [JobController::class, 'delete'])->name('delete');
        });

        Route::group(['as' => 'jobApply.', 'prefix' => 'job-apply'], function () {
            Route::get('/', [JobApplicationController::class, 'list'])->name('list');
            Route::put('/{id}', [JobApplicationController::class, 'update'])->name('update');
            Route::delete('{id}', [JobApplicationController::class, 'delete'])->name('delete');
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
            Route::get('/{id}', [HomeCompanyController::class, 'detail'])->name('detail');
        });

        Route::group(['as' => 'job.', 'prefix' => 'job'], function () {
            Route::get('/', [HomeJobController::class, 'list'])->name('list');
            Route::get('/get-cv', [HomeJobController::class, 'getCv'])->name('getCv')->middleware(['auth:api', 'role:applicant']);
            Route::get('/{id}', [HomeJobController::class, 'detail'])->name('detail');
            Route::post('/apply', [HomeJobController::class, 'apply'])->name('apply');
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
        Route::post('/pdf', [UploadController::class, 'uploadPdf'])->name('pdf');
    }
);
