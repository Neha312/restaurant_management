<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\OrderController;
use App\Http\Controllers\V1\VendorController;
use App\Http\Controllers\V1\StockTypeController;
use App\Http\Controllers\V1\RestaurantController;
use App\Http\Controllers\V1\CousineTypeController;
use App\Http\Controllers\V1\ServiceTypeController;
use App\Http\Controllers\V1\VendorStaffController;
use App\Http\Controllers\V1\RestaurantBillController;
use App\Http\Controllers\V1\RestaurantStockController;
use App\Http\Controllers\V1\RestaurantPictureController;

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

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::controller(ServiceTypeController::class)->prefix('service')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager');
            Route::post('create', 'create')->middleware('check:Admin|Owner|Manager');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager');
            Route::post('update/{id}', 'update')->middleware('check:Admin|Owner|Manager');
            Route::post('delete/{id}', 'delete')->middleware('check:Admin|Owner|Manager');
        });
        Route::controller(StockTypeController::class)->prefix('stock_type')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager');
            Route::post('create', 'create')->middleware('check:Admin|Owner|Manager');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager');
            Route::post('update/{id}', 'update')->middleware('check:Admin|Owner|Manager');
            Route::post('delete/{id}', 'delete')->middleware('check:Admin|Owner|Manager');
        });
        Route::controller(VendorController::class)->prefix('vendor')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager');
            Route::post('create', 'create')->middleware('check:Admin|Owner|Manager');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager');
            Route::post('update/{id}', 'update')->middleware('check:Admin|Owner|Manager');
            Route::post('delete/{id}', 'delete')->middleware('check:Admin|Owner|Manager');
            Route::post('status/{id}', 'status')->middleware('check:Admin|Owner|Manager');
        });
        Route::controller(UserController::class)->prefix('user')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager');
            Route::post('create', 'create')->middleware('check:Admin|Owner|Manager');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager');
            Route::post('update/{id}', 'update')->middleware('check:Admin|Owner|Manager');
            Route::post('delete/{id}', 'delete')->middleware('check:Admin|Owner|Manager');
        });
        Route::controller(RestaurantController::class)->prefix('restaurant')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager');
            Route::post('create', 'create')->middleware('check:Admin');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager');
            Route::post('update/{id}', 'update')->middleware('check:Admin');
            Route::post('delete/{id}', 'delete')->middleware('check:Admin');
        });
        Route::controller(VendorStaffController::class)->prefix('staff')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager|Vendor');
            Route::post('create', 'create')->middleware('check:Vendor');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager|Vendor');
            Route::post('update/{id}', 'update')->middleware('check:Vendor');
            Route::post('delete/{id}', 'delete')->middleware('check:Vendor');
        });
        Route::controller(RestaurantPictureController::class)->prefix('picture')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager');
            Route::post('create', 'create')->middleware('check:Admin|Owner|Manager');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager');
            Route::post('update/{id}', 'update')->middleware('check:Admin|Owner|Manager');
            Route::post('delete/{id}', 'delete')->middleware('check:Admin|Owner|Manager');
        });
        Route::controller(RestaurantStockController::class)->prefix('stock')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager');
            Route::post('create', 'create')->middleware('check:Admin|Owner|Manager');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager');
            Route::post('update/{id}', 'update')->middleware('check:Admin|Owner|Manager');
            Route::post('delete/{id}', 'delete')->middleware('check:Admin|Owner|Manager');
        });
        Route::controller(RestaurantBillController::class)->prefix('bill')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Manager|Vendor');
            Route::post('create', 'create')->middleware('check:Vendor');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager|Vendor');
            Route::post('status/{id}', 'status')->middleware('check:Vendor');
        });
        Route::controller(CousineTypeController::class)->prefix('cousine')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager');
            Route::post('create', 'create')->middleware('check:Admin|Owner|Manager');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager');
            Route::post('update/{id}', 'update')->middleware('check:Admin|Owner|Manager');
            Route::post('delete/{id}', 'delete')->middleware('check:Admin|Owner|Manager');
        });
        Route::controller(OrderController::class)->prefix('order')->group(function () {
            Route::post('list',  'list')->middleware('check:Admin|Owner|Manager|Vendor');
            Route::post('create', 'create')->middleware('check:Admin|Owner|Manager');
            Route::get('get/{id}',  'get')->middleware('check:Admin|Owner|Manager|Vendor');
            Route::post('update/{id}', 'update')->middleware('check:Admin|Owner|Manager');
            Route::post('delete/{id}', 'delete')->middleware('check:Admin|Owner|Manager');
        });
    });
});
