<?php

use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Api\PhoneController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function () {
    Route::post('import-phones', [ImportController::class, 'store'])->name('import.phone.store');
    Route::post('available-phones', [PhoneController::class, 'index'])->name('phone.index');
});
