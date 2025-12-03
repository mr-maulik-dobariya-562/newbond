<?php

use App\Http\Controllers\{
    CommonController,
    DashboardController,
};
use App\Http\Controllers\Application\ItemCategoryController;
use App\Http\Controllers\Masters\ItemController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

/* Common Routes */

Route::get('api', [CommonController::class, 'index'])->name('api');
Route::get('/quotation-print', function () {
    return view('order/quotation_print');
});
Route::middleware('auth')->group(function () {
    /* Dashboard Routes */
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* Master Routes */
    Route::prefix('master')->name('master.')->group(function () {
        /* Item Category List */
        Route::resource('item-category', ItemCategoryController::class);
        Route::post('item-category/get-list', [ItemCategoryController::class, "getList"])->name("item-category.getList");
        Route::get('item-category/delete/{itemCategory}', [ItemCategoryController::class, "destroy"])->name("item-category.delete");

        /* Item Category List */
        Route::resource('item', ItemController::class);
        Route::post('item/get-list', [ItemController::class, "getList"])->name("item.getList");
        Route::get('item/delete/{item}', [ItemController::class, "destroy"])->name("item.delete");
        Route::post('item/model', [ItemController::class, "modelForm"])->name("item.model");
    });

    /* User Management Routes */
    Route::prefix('manage-user')->name("users.")->group(function () {
        /* Users */
        Route::resource('/', UserController::class)->except(['update']);
        Route::put('users/{user}', [UserController::class, "update"])->name("update");
        Route::post('get-list', [UserController::class, "getList"])->name("getList");
        Route::get('delete/{user}', [UserController::class, "destroy"])->name("user.delete");

        /* Roles & Permissions */
        Route::resource('role', RoleController::class);
        Route::post('role/get-list', [RoleController::class, "getList"])->name("role.getList");
        Route::get('role/delete/{role}', [RoleController::class, "destroy"])->name("role.delete");
    });

});
Route::fallback(function () {
    return view('404-page');
});


// Route::get("migrate", function () {
//     Artisan::call("migrate");
// });

/* Authentication Routes  */
require __DIR__ . '/auth.php';
