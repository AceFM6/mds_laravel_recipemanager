<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserController;
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


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

// passport auth api
Route::middleware(['auth:api'])->group(function () {
    Route::get('/', [UserController::class, 'user']);
    Route::get('logout', [UserController::class, 'logout']);

    // recipe resource route
    Route::resource('recipes', RecipeController::class);

    // ingredient route
    Route::get('/recipes/{recipe}/ingredients', [IngredientController::class, 'index']);
    Route::post('/recipes/{recipe}/ingredients', [IngredientController::class, 'store']);
    Route::get('/recipes/{recipe}/ingredients/{ingredient}', [IngredientController::class, 'show']);
    Route::put('/recipes/{recipe}/ingredients/{id}', [IngredientController::class, 'update']);
    Route::delete('/recipes/{recipe}/ingredients/{id}', [IngredientController::class, 'destroy']);



});

