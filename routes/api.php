<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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
Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:api']);

//Category Routes
Route::group(['middleware' => ['verify.token'], 'prefix' => 'category'], function () {
    Route::get('/index', [CategoryController::class, 'index']);
    Route::post('/add', [CategoryController::class, 'store']);
    Route::post('/delete/{id}', [CategoryController::class, 'delete']);
    Route::post('/edit/{id}', [CategoryController::class, 'edit']);
});
//Product Routes
Route::group(['middleware'=>['verify.token'],'prefix' => 'product'],function () {
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
// User Routes
Route::group(['middleware'=>['verify.token'], 'prefix' => 'user'],function () {
    Route::post('/add-to-cart/{product_id}',[CartController::class,'addProductToCart']);// add Product To Cart
    Route::post('/update-cart-item/{product}', [CartController::class, 'updateCartItemQuantity']);
    Route::get('/show-cart-items',[CartController::class,'showProductOnCart']);// Show Cart Items
    Route::post('/delete-from-cart/{product_id}',[CartController::class,'deleteProductFromCart']); //Delete Product From Cart
    Route::get('/home-page',[UserController::class,'homePage']); //Home Page For Auth User
    Route::get('/doctor-list',[UserController::class,'doctorList']); //Home Page For Auth User
    Route::get('/update-personal-details',[UserController::class,'updatePersonalDetails']); //Home Page For Auth User
});
// Pharmacy Routes
Route::group(['middleware'=>['verify.token'],'prefix'=>'pharmacy'],function (){
    route::get('/show-dictionary',[PharmacyController::class,'showDictionary']); // Show Products In Dictionary
    route::get('/search-on-dictionary',[PharmacyController::class,'searchOnDictionary']); // Search in Products In Dictionary
    route::post('/upload-drugs-from-excel-sheet',[PharmacyController::class,'uploadExcelSheet']); //Upload Excel Sheet Contain Products To DB
});
//Chat Routes
Route::group(['middleware'=>['verify.token'],'prefix'=>'chat'],function (){
    Route::post('send-message', [ChatController::class, 'sendMessage']);
    Route::get('get-chat-for-patient', [ChatController::class, 'getChatWithPharmacyForUser']);
    Route::get('get-chat-for-pharmacy', [ChatController::class, 'getChatWithPatientForPharmacy']);
    Route::get('get-patient', [ChatController::class, 'getPatientWhoHasChatWithLoggedPharmacy']);

});

route::get('/show-all-pharmacies',[PharmacyController::class,'showAllPharmacies']); // Show All Pharmacies
route::get('/get-categories-by-pharmacy-id/{pharmacy_id}', [CategoryController::class, 'getCategoriesByPharmacyId']);
Route::get('product/index', [ProductController::class, 'index']);
Route::get('product/get-products-by-category/{category_id}', [ProductController::class, 'productsByCategoryId']);
