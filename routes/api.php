<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GetDepositAddress;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentLink;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TradeController;
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


Route::group(['middleware' => 'web'], function (){
    Route::post('login',    [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('forgot-password', [AuthController::class, 'forgot_password']);
    Route::post('reset-password', [AuthController::class, 'reset_password']);

    /**
     * Authenticated Routes goes here
     */
    Route::group(['middleware' => 'auth:sanctum'], function (){
        Route::post('create-wallet-address', [GetDepositAddress::class, 'getRandomDepositWallet']);
        Route::get('generate-wallet-address', [GetDepositAddress::class, 'store']);
        Route::get('cmd-get/{id}',      [OrderController::class, 'get']);
        Route::get('cmd-get-all',       [OrderController::class, 'get_all']);
        Route::post('cmd-create',       [OrderController::class, 'store']);
        Route::get('cmd-status/{id}',   [OrderController::class, 'status']);

        Route::resource('user', ProfileController::class);
        
        // payment links
        Route::resource('payment-link', PaymentLink::class);
        Route::resource('products', ProductController::class);
        
        Route::group(['prefix' => 'customers'], function (){
            Route::get('/',     [CustomerController::class, 'list'])->name('customers.list');
            Route::get('{id}',  [CustomerController::class, 'show'])->name('customers.show');
        });
        
        // business settings page
        Route::group(['prefix' => 'settings'], function (){
            Route::get('/',         [BusinessController::class, 'show'])->name('settings.get');
            Route::post('update',   [BusinessController::class, 'update'])->name('settings.update');
        });

        Route::group(['prefix' => 'trade'], function (){
            Route::get('history',       [TradeController::class, 'index'])->name('trade.history');
            Route::get('{tradeType}',   [TradeController::class, 'show'])->name('trade.get');
            Route::post('update',       [TradeController::class, 'update'])->name('trade.update');
            Route::post('store',        [TradeController::class, 'store'])->name('trade.store');
            Route::get('delete/{id}',   [TradeController::class, 'destroy'])->name('trade.delete');
        });



        Route::group(['prefix' => 'account'], function (){
            Route::get('balance',           [BalanceController::class, 'balance']);
        });
        

        Route::group(['prefix' => 'compliance'], function (){
            Route::get('/',                 [ComplianceController::class, 'get']);
            Route::post('identity',         [ComplianceController::class, 'identity']);
            Route::post('address',          [ComplianceController::class, 'address']);
            Route::post('directors',        [ComplianceController::class, 'directors']);
            Route::post('share_holders',    [ComplianceController::class, 'share_holders']);
            Route::post('business_docs',    [ComplianceController::class, 'business_docs']);
        });
        
        
        


        // Route::group(['prefix' => 'payment-link'], function (){
        //     Route::post('create', [GetDepositAddress::class, 'getRandomDepositWallet']);
        //     Route::get('update/{id}', [GetDepositAddress::class, 'store']);
        //     Route::get('cmd-get/{id}',      [OrderController::class, 'get']);
        //     Route::get('cmd-get-all',       [OrderController::class, 'get_all']);
        //     Route::post('cmd-create',       [OrderController::class, 'store']);
        //     Route::get('cmd-status/{id}',   [OrderController::class, 'status']);
        // });
    });
});

Route::fallback(function(){
    return get_error_response([
        'message' => 'Page Not Found. If error persists, contact support'], 404);
});