<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    // Route::controller(ServiceTypeController::class)->prefix('service')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager');
    //     Route::post('create', 'create')->middleware('access:Admin');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager');
    //     Route::post('update/{id}', 'update')->middleware('access:Admin');
    //     Route::post('delete/{id}', 'delete')->middleware('access:Admin');
    // });
    // Route::controller(StockTypeController::class)->prefix('stock_type')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager');
    //     Route::post('create', 'create')->middleware('access:Admin');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager');
    //     Route::post('update/{id}', 'update')->middleware('access:Admin');
    //     Route::post('delete/{id}', 'delete')->middleware('access:Admin');
    // });
    // Route::controller(StockController::class)->prefix('stock')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager|Vendor');
    //     Route::post('create', 'create')->middleware('access:Vendor');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager|Vendor');
    //     Route::post('update/{id}', 'update')->middleware('access:Vendor');
    //     Route::post('delete/{id}', 'delete')->middleware('access:Vendor');
    // });
    // Route::controller(UserController::class)->prefix('user')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager');
    //     Route::post('create', 'create')->middleware('access:Admin|Owner|Manager');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager');
    //     Route::post('update/{id}', 'update')->middleware('access:Admin|Owner|Manager');
    //     Route::post('delete/{id}', 'delete')->middleware('access:Admin|Owner|Manager');
    // });
    // Route::controller(CousineTypeController::class)->prefix('cousine')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager');
    //     Route::post('create', 'create')->middleware('access:Admin');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager');
    //     Route::post('update/{id}', 'update')->middleware('access:Admin');
    //     Route::post('delete/{id}', 'delete')->middleware('access:Admin');
    // });
    // Route::controller(RestaurantController::class)->prefix('restaurant')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager');
    //     Route::post('create', 'create')->middleware('access:Admin');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager');
    //     Route::post('update/{id}', 'update')->middleware('access:Admin');
    //     Route::post('delete/{id}', 'delete')->middleware('access:Admin');
    //     Route::get('data/{id}', 'data')->middleware('access:Admin|Owner|Manager');
    // });
    // Route::controller(VendorController::class)->prefix('vendor')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager|Vendor');
    //     Route::post('create', 'create')->middleware('access:Admin|Owner|Manager');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager|Vendor');
    //     Route::post('update/{id}', 'update')->middleware('access:Admin|Owner|Manager');
    //     Route::post('delete/{id}', 'delete')->middleware('access:Admin|Owner|Manager');
    //     Route::post('status/{id}', 'status')->middleware('access:Admin|Owner|Manager');
    // });
    // Route::controller(VendorStaffController::class)->prefix('staff')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager|Vendor');
    //     Route::post('create', 'create')->middleware('access:Vendor');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager|Vendor');
    //     Route::post('update/{id}', 'update')->middleware('access:Vendor');
    //     Route::post('delete/{id}', 'delete')->middleware('access:Vendor');
    // });
    // Route::controller(RestaurantStockController::class)->prefix('rest_stock')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager');
    //     Route::post('create', 'create')->middleware('access:Admin|Owner|Manager');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager');
    //     Route::post('update/{id}', 'update')->middleware('access:Admin|Owner|Manager');
    //     Route::post('delete/{id}', 'delete')->middleware('access:Admin|Owner|Manager');
    // });
    // Route::controller(OrderController::class)->prefix('order')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager|Vendor');
    //     Route::post('create', 'create')->middleware('access:Admin|Owner|Manager');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager|Vendor');
    //     Route::post('status/{id}', 'status')->middleware('access:Vendor');
    // });
    // Route::controller(RestaurantBillController::class)->prefix('bill')->group(function () {
    //     Route::post('list',  'list')->middleware('access:Admin|Owner|Manager|Vendor');
    //     Route::get('get/{id}',  'get')->middleware('access:Admin|Owner|Manager|Vendor');
    // });
}
