<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\OrderController;
use App\Http\Controllers\V1\StockController;
use App\Http\Controllers\V1\VendorController;
use App\Http\Controllers\V1\StockTypeController;
use App\Http\Controllers\V1\RestaurantController;
use App\Http\Controllers\V1\CousineTypeController;
use App\Http\Controllers\V1\ServiceTypeController;
use App\Http\Controllers\V1\VendorStaffController;
use App\Http\Controllers\V1\RestaurantBillController;
use App\Http\Controllers\V1\RestaurantStockController;


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
    Route::post('forgetPassword', [AuthController::class, 'forgetPassword']);
    Route::post('resetPassword/{token}', [AuthController::class, 'resetPassword']);

    Route::get('approve/{id}', [OrderController::class, 'approve'])->name('vendor.approve');
    Route::get('reject/{id}', [OrderController::class, 'reject'])->name('vendor.reject');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('changePassword', [AuthController::class, 'changePassword']);

        Route::group(['prefix' => 'admin', 'middleware' => 'access:Admin'], function () {
            Route::controller(ServiceTypeController::class)->prefix('service')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(StockTypeController::class)->prefix('stock-type')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(StockController::class)->prefix('stock')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(UserController::class)->prefix('user')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(CousineTypeController::class)->prefix('cousine')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(RestaurantController::class)->prefix('restaurant')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
                Route::get('data/{id}', 'data');
            });
            Route::controller(VendorController::class)->prefix('vendor')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(VendorStaffController::class)->prefix('staff')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(RestaurantStockController::class)->prefix('rest-stock')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(OrderController::class)->prefix('order')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(RestaurantBillController::class)->prefix('bill')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
        });
        Route::group(['prefix' => 'owner', 'middleware' => 'access:Owner'], function () {
            Route::controller(ServiceTypeController::class)->prefix('service')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(StockTypeController::class)->prefix('stock-type')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(StockController::class)->prefix('stock')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(UserController::class)->prefix('user')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(CousineTypeController::class)->prefix('cousine')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(RestaurantController::class)->prefix('restaurant')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
                Route::get('data/{id}', 'data');
            });
            Route::controller(VendorController::class)->prefix('vendor')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
                Route::post('status/{id}', 'status');
            });

            Route::controller(VendorStaffController::class)->prefix('staff')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });

            Route::controller(RestaurantStockController::class)->prefix('rest-stock')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(OrderController::class)->prefix('order')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
            });
            Route::controller(RestaurantBillController::class)->prefix('bill')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
        });
        Route::group(['prefix' => 'manager', 'middleware' => 'access:Manager'], function () {
            Route::controller(ServiceTypeController::class)->prefix('service')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(StockTypeController::class)->prefix('stock-type')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(StockController::class)->prefix('stock')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(UserController::class)->prefix('user')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(CousineTypeController::class)->prefix('cousine')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(RestaurantController::class)->prefix('restaurant')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
                Route::post('delete/{id}', 'delete');
                Route::get('data/{id}', 'data');
            });
            Route::controller(VendorController::class)->prefix('vendor')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
                Route::post('status/{id}', 'status');
            });
            Route::controller(VendorStaffController::class)->prefix('staff')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
            Route::controller(RestaurantStockController::class)->prefix('rest-stock')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(OrderController::class)->prefix('order')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
            });
            Route::controller(RestaurantBillController::class)->prefix('bill')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
        });
        Route::group(['prefix' => 'vendor', 'middleware' => 'access:Vendor'], function () {
            Route::controller(StockController::class)->prefix('stock')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(VendorController::class)->prefix('vendor')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
            });
            Route::controller(VendorStaffController::class)->prefix('staff')->group(function () {
                Route::post('list',  'list');
                Route::post('create', 'create');
                Route::get('get/{id}',  'get');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
            });
            Route::controller(OrderController::class)->prefix('order')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
                Route::post('status/{id}', 'status');
            });
            Route::controller(RestaurantBillController::class)->prefix('bill')->group(function () {
                Route::post('list',  'list');
                Route::get('get/{id}',  'get');
            });
        });
    });
});
