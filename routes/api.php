<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PromoCodeController;
//use App\Models\Event;

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
Route::group(['middleware' => 'cors'], function () {
    /*
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
    */
    Route::get('/promo-code', [PromoCodeController::class, 'allCodes'])->name('promo-code');
    Route::put('/promo-code/{id}', [PromoCodeController::class, 'deactivate'])->name('deactivate.promo-code');
    Route::post('/promo-code', [PromoCodeController::class, 'create'])->name('create.promo-code');
    Route::put('/radius/promo-code/{id}', [PromoCodeController::class, 'changeRadius'])->name('radius.promo-code');
    Route::post('/promo', [PromoCodeController::class, 'promo'])->name('use.promo-code');

    /*
    Route::get('/events', function() {
        return response(Event::all(), 200); // used for test case
    });
    */
});