<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\LoginController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\UserController;
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

        //sliders route
        Route::apiResource('/sliders', SliderController::class, ['except' => ['create', 'show', 'edit', 'update'], 'as' => 'admin']);
    });
});
