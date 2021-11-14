<?php

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

//API route for register new user
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register'])->name('login');
//API route for login user
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login'])->name('login');


//Protecting Routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/products', [App\Http\Controllers\API\ProductController::class, 'products']);

    Route::get('/orders/{id?}', [App\Http\Controllers\API\OrderController::class, 'orders']);
    Route::post('/order', [App\Http\Controllers\API\OrderController::class, 'store']);
    Route::put('/order/{order}', [App\Http\Controllers\API\OrderController::class, 'update']);

    // API route for logout user
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
});
