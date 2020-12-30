<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CasesController;

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

Route::prefix('/cases')->group(function () {
    Route::get('/', [CasesController::class, 'index']);
    Route::post('/', [CasesController::class, 'store'])->middleware('validate.request');
    Route::put('/', [CasesController::class, 'update'])->middleware('validate.request');
});

Route::get('/fill_data', [CasesController::class, 'fill']);
