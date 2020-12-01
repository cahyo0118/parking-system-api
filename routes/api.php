<?php


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

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserManagement\ModuleController;
use App\Http\Controllers\api\UserManagement\PermissionController;
use App\Http\Controllers\api\UserManagement\RoleController;
use App\Http\Controllers\api\UserManagement\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::group(['prefix' => 'users'], function () {

        Route::get('/', [UserController::class, 'getAll']);

        Route::get('/{id}/detail', [UserController::class, 'getOne']);

        Route::post('/store', [UserController::class, 'store']);

        Route::put('/{id}/update', [UserController::class, 'update']);

        Route::delete('/{id}/delete', [UserController::class, 'delete']);

    });

    Route::group(['prefix' => 'roles'], function () {

        Route::get('/', [RoleController::class, 'getAll']);

        Route::get('/{id}/detail', [RoleController::class, 'getOne']);

        Route::post('/store', [RoleController::class, 'store']);

        Route::put('/{id}/update', [RoleController::class, 'update']);

        Route::delete('/{id}/delete', [RoleController::class, 'delete']);

    });

    Route::group(['prefix' => 'modules'], function () {

        Route::get('/', [ModuleController::class, 'getAll']);

        Route::get('/{id}/detail', [ModuleController::class, 'getOne']);

        Route::post('/store', [ModuleController::class, 'store']);

        Route::put('/{id}/update', [ModuleController::class, 'update']);

        Route::delete('/{id}/delete', [ModuleController::class, 'delete']);

    });

    Route::group(['prefix' => 'permissions'], function () {

        Route::get('/', [PermissionController::class, 'getAll']);

        Route::get('/{id}/detail', [PermissionController::class, 'getOne']);

        Route::post('/store', [PermissionController::class, 'store']);

        Route::put('/{id}/update', [PermissionController::class, 'update']);

        Route::delete('/{id}/delete', [PermissionController::class, 'delete']);

    });

});
