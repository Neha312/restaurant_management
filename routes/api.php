<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\VendorController;
use App\Http\Controllers\V1\StockTypeController;
use App\Http\Controllers\V1\RestaurantController;
use App\Http\Controllers\V1\ServiceTypeController;

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

Route::prefix('V1')->group(function () {
    Route::post('login', [UserController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::controller(ServiceTypeController::class)->prefix('service')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
        Route::controller(StockTypeController::class)->prefix('stock')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
        });
        Route::controller(VendorController::class)->prefix('vendor')->group(function () {
            Route::post('list',  'list');
            Route::post('create', 'create');
            Route::get('get/{id}',  'get');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
            Route::post('status/{id}', 'status');
        });
        Route::controller(UserController::class)->prefix('user')->group(function () {
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
        });
    });
});
