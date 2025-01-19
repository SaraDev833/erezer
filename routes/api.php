<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\colorController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\userController;
use App\Http\Controllers\Api\UserController as ApiUserController;
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

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('categories' , CategoryController::class);
Route::apiResource('subcategories', SubcategoryController::class);
Route::apiResource('brands', BrandController::class);
Route::apiResource('tags', TagController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('colors', colorController::class);
Route::apiResource('sizes', SizeController::class);
Route::apiResource('users' , UserController::class);


// custom routes
Route::get('categories/{slug}/subcategories', [SubcategoryController::class, 'getByCategory']);
Route::get('products/{id}/variations' , [ProductController::class , 'variation']);
