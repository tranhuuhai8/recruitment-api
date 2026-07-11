<?php

use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CompanyFollowerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobCategoryController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\JobApplicationController as AdminJobApplicationController;
use App\Http\Controllers\Admin\JobFavoriteController as AdminJobFavoriteController;
use App\Http\Controllers\Admin\ApplicationFileController as AdminApplicationFileController;
use App\Http\Controllers\Admin\MailLogController;
use App\Http\Controllers\Admin\MailTemplateController;
use App\Http\Controllers\Applicant\ApplyController;
use App\Http\Controllers\Applicant\AuthController as ApplicantAuthController;
use App\Http\Controllers\Applicant\CompanyFollowController;
use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboardController;
use App\Http\Controllers\Applicant\FavoriteController;
use App\Http\Controllers\Applicant\FileUploadController;
use App\Http\Controllers\Base\UploadController;
use App\Http\Controllers\Company\AuthController as CompanyAuthController;
use App\Http\Controllers\Company\DashboardController as CompanyDashboardController;
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

// Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    // public routes
    Route::controller(UnifiedAuthController::class)->group(function () {
        Route::post('/login', 'login')->name('login')->middleware('throttle:5,1');
        Route::post('/refresh', 'refresh')->name('refresh')->middleware('throttle:15,1');
        Route::post('/forgot-password', 'forgotPassword')->name('forgot_password')->middleware('throttle:3,1');
        Route::post('/reset-password/{token}', 'resetPassword')->name('reset_password')->middleware('throttle:5,1');
        Route::get('/verify-email', 'verifyEmail')->name('verify_email')->middleware('signed');
    });

    // protected routes
    Route::middleware('auth:api')->controller(UnifiedAuthController::class)->group(function () {
        Route::get('/me', 'me')->name('me');
        Route::post('/change-password', 'changePassword')->name('change_password');
        Route::post('/logout', 'logout')->name('logout');
    });

    Route::prefix('company')->name('company.')->controller(CompanyAuthController::class)->group(function () {
        Route::post('/register', 'register')->name('register');
    });

    Route::prefix('applicant')->name('applicant.')->controller(ApplicantAuthController::class)->group(function () {
        Route::post('/register', 'register')->name('register');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'list'])->name('dashboard.list');

    Route::prefix('company')->name('company.')->controller(CompanyController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/get-select', 'listCompany')->name('get_select');
        Route::get('/{id}', 'detail')->name('detail');
        Route::put('/{id}', 'update')->name('update');
    });

    // Followed companies (by applicants)
    Route::get('/company-followers', [CompanyFollowerController::class, 'list'])->name('company_followers.list');

    Route::prefix('applicant')->name('applicant.')->controller(ApplicantController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/{id}', 'detail')->name('detail');
        Route::put('/{id}', 'update')->name('update');
    });

    Route::prefix('job')->name('job.')->controller(AdminJobController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/{id}', 'detail')->name('detail');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'delete')->name('delete');
    });

    Route::prefix('job-apply')->name('job_apply.')->controller(AdminJobApplicationController::class)->group(function () {
        Route::get('/', 'list')->name('list');
    });

    Route::prefix('job-favorite')->name('job_favorite.')->controller(AdminJobFavoriteController::class)->group(function () {
        Route::get('/', 'list')->name('list');
    });

    Route::prefix('application-file')->name('application_file.')->controller(AdminApplicationFileController::class)->group(function () {
        Route::get('/', 'list')->name('list');
    });

    // ── Contact Routes ──────────────────────────────────
    Route::prefix('contact')->name('contact.')->controller(ContactController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/{id}', 'detail')->name('detail');
        Route::put('/{id}', 'update')->name('update');
        Route::post('/{id}/reply', 'reply')->name('reply');
    });

    // ── Mail Template Routes ────────────────────────────
    Route::prefix('mail-template')->name('mail_template.')->controller(MailTemplateController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'detail')->name('detail');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'delete')->name('delete');
        Route::get('/{id}/preview', 'preview')->name('preview');
    });

    // ── Mail Log Routes ─────────────────────────────────
    Route::prefix('mail-log')->name('mail_log.')->controller(MailLogController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/{id}', 'detail')->name('detail');
        Route::post('/{id}/retry', 'retry')->name('retry');
    });

    Route::prefix('master-data')->name('master_data.')->group(function () {
        Route::prefix('cities')->name('cities.')->controller(CityController::class)->group(function () {
            Route::get('/', 'list')->name('list');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'delete')->name('delete');
        });

        Route::prefix('job-categories')->name('job_categories.')->controller(JobCategoryController::class)->group(function () {
            Route::get('/', 'list')->name('list');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'delete')->name('delete');
        });
    });
});

// Applicant Routes
Route::prefix('applicant')->name('applicant.')->middleware(['auth:api', 'role:applicant'])->group(function () {
    Route::put('/update', [ApplicantAuthController::class, 'update'])->name('update');

    Route::get('/dashboard', [ApplicantDashboardController::class, 'list'])->name('dashboard.list');

    Route::prefix('file-upload')->name('file-upload.')->controller(FileUploadController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::put('/upsert', 'upsert')->name('upsert');
    });

    Route::prefix('applied')->name('applied.')->controller(ApplyController::class)->group(function () {
        Route::get('/', 'list')->name('list');
    });

    // Favorites (Saved jobs)
    Route::prefix('favorites')->name('favorites.')->controller(FavoriteController::class)->group(function () {
        Route::get('/jobs', 'listJobs')->name('jobs.list');
        Route::get('/jobs/detail', 'list')->name('jobs.detail');
        Route::post('/jobs/{slug}/toggle', 'toggleJob')->name('jobs.toggle');
    });

    // Follows (Followed companies)
    Route::prefix('follows')->name('follows.')->controller(CompanyFollowController::class)->group(function () {
        Route::get('/companies', 'listCompanies')->name('companies.list');
        Route::get('/companies/detail', 'list')->name('companies.detail');
        Route::post('/companies/{slug}/toggle', 'toggleCompany')->name('companies.toggle');
        Route::post('/companies/{slug}/notify/toggle', 'toggleNotify')->name('companies.notify.toggle');
    });
});

// Company Routes
Route::prefix('company')->name('company.')->middleware(['auth:api', 'role:company'])->group(function () {
    Route::put('/update', [CompanyAuthController::class, 'update'])->name('update');

    Route::get('/dashboard', [CompanyDashboardController::class, 'list'])->name('dashboard.list');

    Route::prefix('job')->name('job.')->controller(JobController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::post('/', 'store')->name('store');
        Route::get('/{slug}', 'detail')->name('detail');
        Route::put('/{slug}', 'update')->name('update');
        Route::delete('/{slug}', 'delete')->name('delete');
    });

    Route::prefix('job-apply')->name('jobApply.')->controller(JobApplicationController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'delete')->name('delete');
    });
});

// Home Routes
Route::prefix('home')->name('home.')->group(function () {
    Route::prefix('master-data')->name('masterData.')->group(function () {
        Route::controller(HomeCityController::class)->group(function () {
            Route::get('/cities', 'list')->name('cities');
            Route::get('/cities-parent', 'listParent')->name('cities_parent');
        });
        Route::controller(HomeJobCategoryController::class)->group(function () {
            Route::get('/job-categories', 'list')->name('job_categories');
            Route::get('/job-categories-parent', 'listParent')->name('job_categories_parent');
        });
    });

    Route::prefix('company')->name('company.')->controller(HomeCompanyController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::get('/{slug}', 'detail')->name('detail');
    });

    Route::prefix('job')->name('job.')->controller(HomeJobController::class)->group(function () {
        Route::get('/', 'list')->name('list');
        Route::post('/apply', 'apply')->name('apply');
        Route::get('/get-cv', 'getCv')->name('getCv')->middleware(['auth:api', 'role:applicant']);
        Route::get('/{slug}', 'detail')->name('detail');
    });

    // ── Contact: public form submission ─────────────────
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
});

// Upload Routes
Route::prefix('upload')->name('upload.')->controller(UploadController::class)->group(function () {
    Route::post('/image', 'uploadImg')->name('image');
    Route::post('/pdf', 'uploadPdf')->name('pdf');
});
