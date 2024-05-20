<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::get('/', function(){
    return response()->json([
        'message' => 'welcome to api Rizki alvian',
    ]);
});


// AUTHORIZATION
Route::get('login',[UserController::class, 'index'])->name('login');
Route::post('login',[UserController::class, 'login']);
Route::post('register',[UserController::class, 'register']);
Route::get('logout',[UserController::class, 'logout']);


// login or register with google
Route::get('oauth/register', [UserController::class, 'loginGoogle']);

Route::group(['middleware' => 'auth:api'], function () {
    // logout
    Route::post('logout', [UserController::class, 'logout']);

    // products
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);

    // CRUD ADMIN
    Route::middleware('admin')->group(function () {
        Route::get('categories', [CategoryController::class, 'index']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    });
});
