<?php

use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([], function(){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});


Route::prefix('posts')->middleware('auth:sanctum')->group(function () {
    Route::get('/all', [PostController::class, 'index']);
    Route::post('/create', [PostController::class, 'store']);
    Route::post('/update/{id}', [PostController::class, 'update']);
    Route::delete('/delete/{id}', [PostController::class, 'destroy']);
});


Route::prefix('comments')->middleware('auth:sanctum')->group(function () {
    Route::post('/add', [CommentController::class, 'store']);
    Route::delete('/delete/{id}', [CommentController::class, 'destroy']);
});
