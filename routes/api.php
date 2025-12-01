<?php

use App\Http\Controllers\Api\{
    ArrivalController,
    CatalogueController,
    EstimateController,
    ForgetPasswordController,
    LocationController,
    LoginController,
    MasterController,
    OrderController,
    PaymentController,
    ProductController,
    ProfileController,
    RegistrationController,
    VersionController,
    WalletController,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('login', [LoginController::class, "index"]);
    Route::post('forget-password', [ForgetPasswordController::class, "index"]);
    Route::post('register', [RegistrationController::class, 'index']);
    Route::post('change-password', [RegistrationController::class, 'updatePassword']);
    Route::post('arrival', [ArrivalController::class, "index"]);
    Route::post('catalogue', [CatalogueController::class, "index"]);
    Route::get('get-state-city-list', [LocationController::class, 'index']);
    Route::post('upload-data', [VersionController::class, 'index']);
    Route::post('upload-profile', [ProfileController::class, 'index']);
    Route::post('product', [ProductController::class, 'index']);
    Route::post('order', [OrderController::class, 'index']);
    Route::post('estimate', [EstimateController::class, 'index']);
    Route::post('catalogue', [CatalogueController::class, 'index']);
    Route::post('wallet', [WalletController::class, 'index']);
    Route::post('pending-list', [OrderController::class, 'pending_list']);
    Route::post('past-list', [OrderController::class, 'past_list']);
    Route::post('payment', [PaymentController::class, 'index']);
    Route::post('offers', [MasterController::class, 'index']);
    Route::post('termsConditions', [MasterController::class, 'termsConditions']);
    Route::post('feedback', [MasterController::class, 'feedback']);
});
