<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GetDepositAddress;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentLink;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\TradeHistoryController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\SwapController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\DojaController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;

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

Route::group(['middleware' => 'web', 'prefix' => 'checkout'], function() {
    Route::get("{slug}",   [CheckoutController::class, 'getPaymentData'])->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post("{slug}",  [CheckoutController::class, 'process'])->withoutMiddleware([VerifyCsrfToken::class]);
});


Route::group(['middleware' => 'web'], function (){
    Route::post('login',                [AuthController::class, 'login'])->name('login');
    Route::post('register',             [AuthController::class, 'register'])->name('register');
    Route::post('verify-email',         [AuthController::class, 'verify_email']);
    Route::post('resend-verify-email',  [AuthController::class, 'resend_verification_email']);
    
    Route::post('forgot-password', [ForgotPassword::class, 'reset_password_request']);
    Route::post('reset-password',  [ForgotPassword::class, 'reset_password_submit']);


    /**
     * Authenticated Routes goes here
     */
    Route::group(['middleware' => 'auth:sanctum'], function (){
        // mobile app endpoint
        Route::any("checkout/mobile/{type}",  [CheckoutController::class, 'mobile']);
        // wallet topup via crypto
        Route::any("topup/balance",     [DepositController::class, 'topup']);
        
        // wallet management routes
        Route::post('create-wallet-address',    [GetDepositAddress::class, 'getRandomDepositWallet']);
        Route::get('generate-wallet-address',   [GetDepositAddress::class, 'store']);
        Route::get('cmd-get/{id}',              [OrderController::class, 'get']);
        Route::get('cmd-get-all',               [OrderController::class, 'get_all']);
        Route::post('cmd-create',               [OrderController::class, 'store']);
        Route::get('cmd-status/{id}',           [OrderController::class, 'status']);

        Route::resource('user', ProfileController::class);
        
        Route::post('update-profile',   [AuthController::class, 'updateProfile']);
        Route::post('send-otp',         [DojaController::class, 'send_otp'])->name('send.otp');
        Route::post('validate-otp',     [DojaController::class, 'validate_otp'])->name('validate.otp');
        Route::post('pin/{new_pin}',    [AuthController::class, 'transaction_pin']);
        
        // payment links
        Route::resource('payment-link', PaymentLink::class);
        Route::resource('products', ProductController::class);
        
        Route::group(['prefix' => 'customers'], function (){
            Route::get('/',                 [CustomerController::class, 'list'])->name('customers.list');
            Route::get('{id}',              [CustomerController::class, 'show'])->name('customers.show');
            Route::get('orders/{email}',    [CustomerController::class, 'customer'])->name('customers.orders');
        });
        
        Route::group(['prefix' => 'swap'], function (){
            Route::post('currency',         [SwapController::class, 'swap'])->name('swap.get');
            Route::post('currency/process', [SwapController::class, 'process_swap'])->name('swap.post');
        });
        
        // business settings page
        Route::group(['prefix' => 'settings'], function (){
            Route::get('/',         [BusinessController::class, 'show'])->name('settings.get');
            Route::post('update',   [BusinessController::class, 'update'])->name('settings.update');
        });
        
        // Trade History
        Route::group(['prefix' => 'trade/history'], function (){
            Route::get('buy-sell-history',  [TradeHistoryController::class, 'index'])->name('get');
            Route::post('buy-sell-order',   [TradeHistoryController::class, 'store'])->name('trade.order');
            Route::get('get-order/{id}',    [TradeHistoryController::class, 'getTrade'])->name('get.trade.order');
            Route::get('delete/{id}',       [TradeHistoryController::class, 'destroy'])->name('trade.order.delete');
        });



        Route::group(['prefix' => 'account'], function (){
            Route::get('balance',           [BalanceController::class, 'balance']);
            Route::get('total-balance',     [BalanceController::class, 'total_balance']);
        });

        Route::group(['prefix' => 'dispute'], function (){
            Route::get('/',                 [DisputeController::class, 'dispute']);
            Route::post('/',                [DisputeController::class, 'dispute']);
            Route::post('reply/{id}',       [DisputeController::class, 'reply']);
            Route::post('close/{id}',       [DisputeController::class, 'close']);
            Route::post('resolve/{id}',     [DisputeController::class, 'resolve']);
            Route::get('chats/{disputeId}', [DisputeController::class, 'chats']);
        });

        Route::group(['prefix' => 'payment-method'], function (){
            Route::get('/',             [PaymentMethodController::class, 'index']);
            Route::post('/',            [PaymentMethodController::class, 'store']);
            Route::get('/{id}/delete',  [PaymentMethodController::class, 'destroy']);
            
            // system add currencies supported data
            Route::get('sys-currencies',            [PaymentMethodController::class, 'sys_currencies']);
        });
        

        Route::group(['prefix' => 'compliance'], function (){
            Route::get('/',                 [ComplianceController::class, 'get']);
            Route::post('identity',         [ComplianceController::class, 'identity']);
            Route::post('agent',            [ComplianceController::class, 'agent']);
            Route::post('update/profile',   [ComplianceController::class, 'update']);
            Route::post('personal/profile', [ComplianceController::class, 'profile']);
            Route::post('address',          [ComplianceController::class, 'address']);
            Route::post('directors',        [ComplianceController::class, 'directors']);
            Route::post('share_holders',    [ComplianceController::class, 'share_holders']);
            Route::post('business_docs',    [ComplianceController::class, 'business_docs']);
        });
        
        
        Route::group(['prefix' => 'withdraw'], function (){
            Route::post('/',            [PayoutController::class, 'withdraw']);
            Route::get('history',       [PayoutController::class, 'withdraw_history']);
        });
        
        Route::group(['prefix' => 'trade'], function (){
            Route::post('paid',     [TradeHistoryController::class, 'paid']);
            Route::post('received', [TradeHistoryController::class, 'received']);
            
            Route::any('trade-status', [TradeHistoryController::class, 'trade_status']);
        });

        Route::group(['prefix' => 'trade'], function (){
            Route::get('history',       [TradeController::class, 'index'])->name('trade.history');
            Route::post('update',       [TradeController::class, 'update'])->name('trade.update');
            Route::post('store',        [TradeController::class, 'store'])->name('trade.store');
            Route::get('delete/{id}',   [TradeController::class, 'destroy'])->name('trade.delete');
            Route::get('deleted',       [TradeController::class, 'deleted'])->name('trade.deleted');
            Route::get('open/agent',    [TradeController::class, 'agent'])->name('trade.open.agent');
            Route::get('open/user',     [TradeController::class, 'user'])->name('trade.open.users');
            Route::get('{tradeType}',   [TradeController::class, 'show'])->name('trade.get');
        });

    });
});

Route::fallback(function(){
    return get_error_response([
        'message' => 'Page Not Found. If error persists, contact support'], 404);
});