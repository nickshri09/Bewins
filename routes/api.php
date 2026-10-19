<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientAuthController;
use App\Http\Controllers\Api\ClientFDController;
use App\Http\Controllers\Api\ClientInvestmentController;
use App\Http\Controllers\Api\ClientLoanController;
use App\Http\Controllers\Api\ClientInvestmentExpectedReturnController;
use App\Http\Controllers\Api\MemberWalletController;
use App\Http\Controllers\Api\MobileOrderController;
use App\Http\Controllers\Api\ClientReferralController;
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


Route::post('client/register', [ClientAuthController::class, 'register']);
Route::post('client/login', [ClientAuthController::class, 'login']);

// Forget Password Flow Routes
Route::post('client/forgot-password', [ClientAuthController::class, 'forgotPassword']);
Route::post('client/verify-otp', [ClientAuthController::class, 'verifyOTP']);
Route::post('client/reset-password', [ClientAuthController::class, 'resetPassword']);

// Protected routes - authentication handled in controller methods
Route::post('client/logout', [ClientAuthController::class, 'logout']);


Route::post('client/update-profile', [ClientAuthController::class, 'profile_update']);

Route::get('client/branches', [ClientAuthController::class, 'getBranches']);

Route::get('client/profile', [ClientAuthController::class, 'profile_show']);

Route::post('client/storefd',[ClientFDController::class,'store']);

Route::get('client/showFD',[ClientFDController::class, 'index']);

// Route::get('client/show/{id}',[ClientFDController::class, 'show']);
Route::get('client/show/',[ClientFDController::class, 'show']);


// Investment
Route::post('client/store-invest',[ClientInvestmentController::class,'store']);
Route::get('client/all-invest',[ClientInvestmentController::class,'all_invest']);
Route::get('client/show-invest/{id}',[ClientInvestmentController::class,'showBy_Id']);


//loan
Route::post('client/store-loan',[ClientLoanController::class, 'store']);
Route::get('client/show-all-loans',[ClientLoanController::class,'show_all_loans']);
Route::get('client/loans/{id}',[ClientLoanController::class,'show_single_loan']);
Route::get('loan/product',[ClientLoanController::class,'loan_products']);


//expected return
Route::get('client/expected-return',[ClientInvestmentExpectedReturnController::class,'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('client/referrals/summary', [ClientReferralController::class, 'summary']);

    Route::get('client/wallet/summary', [MemberWalletController::class, 'summary']);
    Route::get('client/wallet/transactions', [MemberWalletController::class, 'transactions']);
    Route::get('client/wallet/investment-history', [MemberWalletController::class, 'investmentInterestHistory']);
    // Route::get('client/wallet/add-money', [MemberWalletController::class, 'addMoney']);
     Route::post('client/wallet/add-money', [MemberWalletController::class, 'addMoney']);
    Route::post('client/wallet/verify-payment', [MemberWalletController::class, 'verifyPayment']);

    // Withdrawals & Investments
    Route::get('client/investments/list', [\App\Http\Controllers\Api\WithdrawalController::class, 'investmentList']);
    Route::post('client/withdrawal/request', [\App\Http\Controllers\Api\WithdrawalController::class, 'createRequest']);
    Route::get('client/withdrawal/requests', [\App\Http\Controllers\Api\WithdrawalController::class, 'myRequests']);
});

// Mobile Order API Routes
Route::middleware('auth:sanctum')->group(function () {
    // Get all service categories
    Route::get('client/order/categories', [MobileOrderController::class, 'getCategories']);

    // Get services by category and subcategory
    Route::get('client/order/services/{category}/{subCategory?}', [MobileOrderController::class, 'getServices']);

    // Get service details
    Route::get('client/order/service-details/{id}', [MobileOrderController::class, 'getServiceDetails']);

    // Create Razorpay order for payment
    Route::post('client/order/create-razorpay-order', [MobileOrderController::class, 'createRazorpayOrder']);

    // Create Order using Wallet Balance
    Route::post('client/order/create-wallet-order', [MobileOrderController::class, 'createOrderWithWallet']);

    // Verify Razorpay payment and complete order
    Route::post('client/order/verify-payment', [MobileOrderController::class, 'verifyPayment']);

    // Get user's orders
    Route::get('client/orders', [MobileOrderController::class, 'getOrders']);

    // Get single order details
    Route::get('client/order/{id}', [MobileOrderController::class, 'getOrderDetails']);
});
