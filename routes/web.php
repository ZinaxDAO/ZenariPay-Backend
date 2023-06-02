<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GetDepositAddress;
use App\Http\Middleware\VerifyCsrfToken;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Artisan::call('order:update-status');
    return redirect()->to('https://zinari.org');
    return get_error_response(['msg' => 'Access Denied.'], 401);
});



Route::get("checkout/{slug}",   [CheckoutController::class, 'getPaymentData']);
Route::post("checkout/{slug}",  [CheckoutController::class, 'process']);


// Route::get("gen-wallet",  [GetDepositAddress::class, 'store']);

 
// let's handle webhook here
// Route::group([], function(){
//     Route::any("webhook", [WebhookController::class, 'webhook']);
// })->withoutMiddleware([VerifyCsrfToken::class]);

Route::any("webhook", [WebhookController::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class]);