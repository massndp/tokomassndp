<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\InvoiceController;
use App\Http\Controllers\Api\Admin\LoginController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Api\Customer\InvoiceController as CustomerInvoiceController;
use App\Http\Controllers\Api\Customer\LoginController as CustomerLoginController;
use App\Http\Controllers\Api\Customer\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function () {


    Route::post('/login', [LoginController::class, 'index', ['as' => 'admin']]);

    Route::group(['middleware' => 'auth:api_admin'], function () {

        //Credentials route
        Route::get('/user', [LoginController::class, 'getUser', ['as' => 'admin']]);
        Route::get('/refresh', [LoginController::class, 'refreshToken', ['as' => 'admin']]);
        Route::post('/logout', [LoginController::class, 'logout', ['as' => 'admin']]);

        //Dashboard route
        Route::get('/dashboard', [DashboardController::class, 'index', ['as' => 'admin']]);

        //User route
        Route::apiResource('/users', UserController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        //Category route
        Route::apiResource('/categories', CategoryController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        //Products route
        Route::apiResource('/products', ProductController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        //Invoice route
        Route::apiResource('/invoices', InvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'admin']);

        //customer route
        Route::apiResource('/customers', CustomerController::class, ['except' => ['create', 'show', 'store', 'edit', 'update', 'destroy'], 'as' => 'admin']);

        //sliders route
        Route::apiResource('/sliders', SliderController::class, ['except' => ['create', 'show', 'edit', 'update'], 'as' => 'admin']);
    });
});

Route::prefix('customer')->group(function () {

    Route::post('/register', [RegisterController::class, 'store'], ['as' => 'admin']);

    //login
    Route::post('/login', [CustomerLoginController::class, 'index'], ['as' => 'customer']);

    Route::group(['middleware' => 'auth:api_customer'], function () {

        //getcustomer route
        Route::get('/get-customer', [CustomerLoginController::class, 'getCustomer'], ['as' => 'customer']);

        //refresh token route
        Route::get('/refresh-token', [CustomerLoginController::class, 'refreshToken'], ['as' => 'customer']);

        //logout route
        Route::post('/logout', [CustomerLoginController::class, 'logout'], ['as' => 'customer']);

        //Dashboard Route
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'], ['as' => 'customer']);

        //invoices
        Route::apiResource('/invoices', CustomerInvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'customer']);
    });
});
