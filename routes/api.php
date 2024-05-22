<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])
        ->middleware(['auth:api']);

//Category Routes
Route::group(['middleware' => ['verify.token'], 'prefix' => 'category'], function () {
    Route::get('/index', [CategoryController::class, 'index']);
    Route::post('/add', [CategoryController::class, 'store']);
    Route::post('/delete/{id}', [CategoryController::class, 'delete']);
    Route::post('/edit/{id}', [CategoryController::class, 'edit']);
});
//Product Routes
Route::group(['middleware'=>['verify.token'],'prefix' => 'product'],function () {
    Route::get('/index', [ProductController::class, 'index']);
    Route::get('/get-products-by-category/{category_id}', [ProductController::class, 'productsByCategoryId']);
    Route::get('/get-product-by-id/{id}', [ProductController::class, 'getProductById']);
    Route::post('/add', [ProductController::class, 'store']);
    Route::post('/delete/{id}', [ProductController::class, 'delete']);
    Route::post('/edit/{id}', [ProductController::class, 'edit']);
});

// Favorite Routes
Route::group(['middleware'=>['verify.token'],'prefix' => 'favorite'],function () {
    Route::get('/favorite-products',[FavoriteController::class,'getFavoriteProducts']);// Get ALL Favorite Products For Logged User
    Route::post('/add-favorite-products',[FavoriteController::class,'storeFavoriteProducts']);// Store Favorite Products For Logged User
    Route::post('/delete-favorite-products/{id}',[FavoriteController::class,'deleteFavoriteProducts']); //Delete Favorite Products For Logged User
});