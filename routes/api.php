<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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

Route::post('/login',       [LoginController::class, 'login']);
Route::post('/register',    [RegisterController::class, 'register']);


Route::controller(LoginController::class)->middleware('auth:api')->group(function () {
    Route::get('/me',                   'userDetails')->name('user.details');
    Route::get('/logout',              'logout')->name('user.logout');
    Route::get('/check-login',          'checkLogin')->name('user.checkLogin');
});

Route::controller(CategoryController::class)->prefix('category')->group(function () {
    Route::get('/list',             'index')->name('category.list');
    Route::post('/store',           'store')->name('category.store');
    Route::get('/{id}/show',             'show')->name('category.show');
    Route::get('/htmltree',         'getCategoryHtmlTree')->name('category.htmltree');
    Route::put('/{id}/update',             'update')->name('category.update');


    Route::delete('/{id}',          'destroy')->name('category.destroy');
});
