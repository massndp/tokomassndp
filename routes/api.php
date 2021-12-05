<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\LoginController;
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
        Route::get('/user', [LoginController::class, 'getUser', ['as' => 'admin']]);
        Route::get('/refresh', [LoginController::class, 'refreshToken', ['as' => 'admin']]);
        Route::post('/logout', [LoginController::class, 'logout', ['as' => 'admin']]);

        Route::get('/dashboard', [DashboardController::class, 'index', ['as' => 'admin']]);

        Route::apiResource('/users', UserController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);

        Route::apiResource('/sliders', SliderController::class, ['except' => ['create', 'show', 'edit', 'update'], 'as' => 'admin']);
    });
});
