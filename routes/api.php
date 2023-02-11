<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\Auth\LoginController;

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

Route::prefix('v1')->group(function () {

    Route::post('login', [LoginController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::prefix('task')->group(function () {
            Route::get('my',[TaskController::class, 'myTask'] );
            Route::Post('add',[TaskController::class, 'add'] );
            Route::get('edit/{id}',[TaskController::class, 'edit'] );
            Route::Post('update',[TaskController::class, 'update'] );
            Route::Post('mark-unmark',[TaskController::class, 'markUnmark'] );
        });
    });
});
