<?php

use App\Http\Controllers\Admin\FdAgreementController;
use App\Http\Controllers\Admin\InvestmentAgreementController;
use App\Http\Controllers\Admin\InvestmentExpectedReturnController;
use App\Http\Controllers\Admin\LiveBannerController;
use App\Http\Controllers\Admin\DashboardTickerController;
use App\Http\Controllers\Admin\knowledgeBaseController;
use App\Http\Controllers\Admin\LoanAgreementController;
use App\Http\Controllers\Admin\PricingPlanController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\Admin\WithdrawRequestController as AdminWithdrawRequestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Services\MainServiceController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BasicLicenceController;
use App\Http\Controllers\BewinsMainServiceController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\CustomServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositMethodController;
use App\Http\Controllers\DepositRequestController;
use App\Http\Controllers\EmailNotificationController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FDController;
use App\Http\Controllers\GuarantorController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LoanCollateralController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanPaymentController;
use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberDocumentController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\OrderPlacedController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SavingsAccountController;
use App\Http\Controllers\SavingsProductController;
use App\Http\Controllers\Select2Controller;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SidebarPermissionController;
use App\Http\Controllers\TaxationOrderController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\WithdrawMethodController;
use App\Http\Controllers\WithdrawRequestController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | contains the "web" middleware group. Now create something great!
 * |
 */

Route::group(['middleware' => ['install']], function () {
    // Manish
    Route::get('/', function () {
        return redirect('login');
    });

    Auth::routes();
    Route::get('/logout', [LoginController::class, 'logout'])->name('login-page');

    // Email OTP Login Routes
    Route::post('/login/send-otp', [LoginController::class, 'sendOtp'])->name('login.send-otp');
    Route::post('/login/otp', [LoginController::class, 'loginWithOtp'])->name('login.otp');

    Route::get('verify_2fa/resend', [TwoFactorController::class, 'resend'])->name('verify_2fa.resend');
    Route::get('verify_2fa', [TwoFactorController::class, 'index'])->name('verify_2fa.index');
    Route::post('verify_2fa/verify', [TwoFactorController::class, 'verify'])->name('verify_2fa.verify');

    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::group(['middleware' => ['Email2FA']], function () {
            // Manish
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

            // Knowledge Page Route
            Route::get('/knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
            Route::get('/knowledge/plan/{plan}', [KnowledgeController::class, 'showPlan'])->name('knowledge.plan.show');
            
            // Admin Pricing Plans Routes
            Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
                Route::resource('pricing-plans', PricingPlanController::class);
                Route::patch('pricing-plans/{pricingPlan}/toggle-status', [PricingPlanController::class, 'toggleStatus'])->name('pricing-plans.toggle-status');
                Route::resource('knowledge-base', knowledgeBaseController::class)->parameters(['knowledge-base' => 'knowledge']);;

                // Toggle active/inactive status
                Route::patch('knowledge-base/{knowledge}/toggle-status', [knowledgeBaseController::class, 'toggleStatus'])
                    ->name('knowledge.toggle-status');
            });

            Route::get('/addservice', [ServiceController::class, 'create'])->name('addservices.create');  // ✅ Add this
            Route::post('/addservice', [ServiceController::class, 'store'])->name('addservices.store');
            Route::get('/viewservice', [ServiceController::class, 'index'])->name('addservices.index');
            Route::get('/editservice/{batchId}', [ServiceController::class, 'edit'])->name('addservices.edit');
            Route::put('/updateservice/{batchId}', [ServiceController::class, 'update'])->name('addservices.update');

            Route::get('/importservice', [ServiceController::class, 'importView'])->name('addservices.import');
            Route::post('/importservice', [ServiceController::class, 'import'])->name('addservices.import.store');

            Route::get('/download', [ServiceController::class, 'downloadView'])->name('services.download');

            // Main Services CRUD bewins for services
            // Route::prefix('main-services')->middleware(['auth', 'verified'])->group(function () {
            //     Route::get('/', [BewinsMainServiceController::class, 'index'])->name('main-services.index');
            //     Route::get('/create', [BewinsMainServiceController::class, 'create'])->name('main-services.create');
            //     Route::post('/', [BewinsMainServiceController::class, 'store'])->name('main-services.store');
            //     Route::get('/{id}/edit', [BewinsMainServiceController::class, 'edit'])->name('main-services.edit');
            //     Route::put('/{id}', [BewinsMainServiceController::class, 'update'])->name('main-services.update');
            //     Route::delete('/{id}', [BewinsMainServiceController::class, 'destroy'])->name('main-services.destroy');
            // });

            Route::get('/fssai', function () {
                return view('layouts.menus.fssai');
            })->name('fssai.index');

            Route::get('/view_orders', function () {
                return view('layouts.menus.view_orders');
            })->name('view_orders');

            Route::get('/orders-simple', [OrderPlacedController::class, 'dropdownindexSimple'])->name('orders.index.simple');

            Route::get('/get_percent/{id}', [OrderPlacedController::class, 'getPercent'])->name('get_percent.index');

            Route::get('/wallet', function () {
                return view('layouts.menus.wallet');
            })->name('wallet');

            Route::get('/request_withdraw', function () {
                return view('layouts.menus.request_withdraw');
            })->name('request_withdraw');

            Route::get('/add_money', [WalletController::class, 'addMoneyView'])->name('add_money');

            Route::post('/razorpay/payment-success', [WalletController::class, 'handleRazorpaySuccess'])->name('razorpay.payment.success');

            Route::get('/select-service', [App\Http\Controllers\BasicLicenceController::class, 'showServiceForm'])->name('service.select');

            Route::get('/basic_licence/{id}', [BasicLicenceController::class, 'show'])->name('basic.licence');

            Route::post('/store-custom-service', [CustomServiceController::class, 'store'])->name('custom.service.store');
            Route::get('/custom-services', [CustomServiceController::class, 'index'])->name('custom-services.index');

            Route::post('/order-placed', [OrderPlacedController::class, 'store'])->name('orderPlaced.store');
            Route::get('/order/create', [OrderPlacedController::class, 'create'])->name('order.create');
            Route::get('/view_orders', [OrderPlacedController::class, 'index'])->name('orders.index');
            Route::delete('/orders/{id}', [OrderPlacedController::class, 'destroy'])->name('orders.destroy');
            Route::get('/view_order_single/{id}', [OrderPlacedController::class, 'singleShow'])->name('orders.show');

            Route::post('/orders/{id}/chat', [OrderPlacedController::class, 'storeChat'])->name('order.chat.store');

            Route::get('/orders', [OrderPlacedController::class, 'dropdownindex'])->name('dropdownindex');

            // Route::get('dashboard', function(){
            //     return "here";
            // })->name('dashboard.index');

            Route::get('/wallet', [WalletController::class, 'walletTransaction'])->name('walle.showwww');
            Route::get('/wallet/history', [WalletController::class, 'history'])->name('wallets.history');

            // Profile Controller
            Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
            Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update')->middleware('demo');
            Route::get('profile/change_password', [ProfileController::class, 'change_password'])->name('profile.change_password');
            Route::post('profile/update_password', [ProfileController::class, 'update_password'])->name('profile.update_password')->middleware('demo');
            Route::get('profile/notification_mark_as_read/{id}', [ProfileController::class, 'notification_mark_as_read'])->name('profile.notification_mark_as_read');
            Route::get('profile/show_notification/{id}', [ProfileController::class, 'show_notification'])->name('profile.show_notification');

            /** Admin Only Route */
            Route::group(['middleware' => ['admin', 'demo'], 'prefix' => 'admin'], function () {
                Route::get('ticker-messages', [DashboardTickerController::class, 'index'])->name('admin.tickers.index');
                Route::get('ticker-messages/create', [DashboardTickerController::class, 'create'])->name('admin.tickers.create');
                Route::post('ticker-messages', [DashboardTickerController::class, 'store'])->name('admin.tickers.store');
                Route::get('ticker-messages/{ticker}/edit', [DashboardTickerController::class, 'edit'])->name('admin.tickers.edit');
                Route::put('ticker-messages/{ticker}', [DashboardTickerController::class, 'update'])->name('admin.tickers.update');
                Route::delete('ticker-messages/{ticker}', [DashboardTickerController::class, 'destroy'])->name('admin.tickers.destroy');

                Route::get('banners', [LiveBannerController::class, 'index'])->name('admin.banners.index');
                Route::get('banners/create', [LiveBannerController::class, 'create'])->name('admin.banners.create');
                Route::post('banners', [LiveBannerController::class, 'store'])->name('admin.banners.store');
                Route::get('banners/{banner}/edit', [LiveBannerController::class, 'edit'])->name('admin.banners.edit');
                Route::put('banners/{banner}', [LiveBannerController::class, 'update'])->name('admin.banners.update');
                Route::delete('banners/{banner}', [LiveBannerController::class, 'destroy'])->name('admin.banners.destroy');

                // User Management
                Route::resource('users', UserController::class);
                // User Roles
                Route::resource('roles', RoleController::class);

                Route::post('roles/get_role_name', [RoleController::class, 'getRoleName'])->name('roles.get_role_name');

                // Payment Gateways
                Route::resource('payment_gateways', PaymentGatewayController::class)->except([
                    'create',
                    'store',
                    'show',
                    'destroy',
                ]);

                // Branch Controller
                Route::resource('branches', BranchController::class);

                // Savings Products
                Route::resource('savings_products', SavingsProductController::class);

                // Transaction Category
                Route::resource('transaction_categories', TransactionCategoryController::class);

                // Loan Products
                Route::resource('loan_products', LoanProductController::class);

                // Expense Categories
                Route::resource('expense_categories', ExpenseCategoryController::class)->except('show');

                // Currency List
                Route::resource('currency', CurrencyController::class);

                // Deposit Methods
                Route::resource('deposit_methods', DepositMethodController::class)->except([
                    'show',
                ]);

                // Withdraw Methods
                Route::resource('withdraw_methods', WithdrawMethodController::class)->except([
                    'show',
                ]);

                // Permission Controller
                Route::get('permission/access_control', [PermissionController::class, 'index'])->name('permission.index');
                Route::get('permission/access_control/{user_id?}', [PermissionController::class, 'show'])->name('permission.show');
                Route::post('permission/store', [PermissionController::class, 'store'])->name('permission.store');

                // Sidebar Permission Controller (New Clean System)
                Route::get('sidebar-permissions/{role_id?}', [SidebarPermissionController::class, 'index'])->name('sidebar.permissions.index');
                Route::post('sidebar-permissions', [SidebarPermissionController::class, 'store'])->name('sidebar.permissions.store');

                // Language Controller
                Route::resource('languages', LanguageController::class);

                // Utility Controller
                Route::match(['get', 'post'], 'administration/general_settings/{store?}', [UtilityController::class, 'settings'])->name('settings.update_settings');
                Route::post('administration/upload_logo', [UtilityController::class, 'upload_logo'])->name('settings.uplaod_logo');
                Route::get('administration/database_backup_list', [UtilityController::class, 'database_backup_list'])->name('database_backups.list');
                Route::get('administration/create_database_backup', [UtilityController::class, 'create_database_backup'])->name('database_backups.create');
                Route::delete('administration/destroy_database_backup/{id}', [UtilityController::class, 'destroy_database_backup'])->name('database_backups.destroy_database_backup');
                Route::get('administration/download_database_backup/{id}', [UtilityController::class, 'download_database_backup'])->name('database_backups.download');
                Route::post('administration/remove_cache', [UtilityController::class, 'remove_cache'])->name('settings.remove_cache');
                Route::post('administration/send_test_email', [UtilityController::class, 'send_test_email'])->name('settings.send_test_email');

                // Notification Template
                Route::resource('notification_templates', NotificationTemplateController::class)->only([
                    'index',
                    'edit',
                    'update',
                ]);
            });

            // deletes

            //   DESTROY
            Route::delete('custom_fields/{custom_field}', [CustomFieldController::class, 'destroy'])
                ->name('custom_fields.destroy')
                ->middleware('demo');

            Route::delete('member_documents/{member_document}', [MemberDocumentController::class, 'destroy'])
                ->name('member_documents.destroy');

            Route::delete('savings_accounts/{savings_account}', [SavingsAccountController::class, 'destroy'])
                ->middleware('demo')
                ->name('savings_accounts.destroy');

            Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])
                ->name('transactions.destroy');

            Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])
                ->name('expenses.destroy');

            Route::delete('loans/{loan}', [LoanController::class, 'destroy'])
                ->name('loans.destroy');

            Route::delete('guarantors/{guarantor}', [GuarantorController::class, 'destroy'])
                ->name('guarantors.destroy');

            Route::delete('loan_payments/{loan_payment}', [LoanPaymentController::class, 'destroy'])
                ->name('loan_payments.destroy');

            Route::delete('admin/main-services/{id}', [BewinsMainServiceController::class, 'destroy'])->name('main-services.destroy');

            /** Dynamic Permission */
            Route::group(['middleware' => ['permission'], 'prefix' => 'admin'], function () {
                // Add the my-agents route here

                Route::get('users/my-agents', [UserController::class, 'myAgents'])->name('users.my_agents');

                // Salary Management
                Route::get('salaries/get_attendance', [SalaryController::class, 'get_attendance'])->name('salaries.get_attendance');
                Route::resource('salaries', SalaryController::class);
                Route::get('attendances/user/{id}', [AttendanceController::class, 'user_attendance'])->name('attendances.user_attendance');
                Route::resource('attendances', AttendanceController::class);

                // Dashboard Widget
                Route::get('dashboard/total_customer_widget', [DashboardController::class, 'total_customer_widget'])->name('dashboard.total_customer_widget');
                Route::get('dashboard/deposit_requests_widget', [DashboardController::class, 'deposit_requests_widget'])->name('dashboard.deposit_requests_widget');
                Route::get('dashboard/withdraw_requests_widget', [DashboardController::class, 'withdraw_requests_widget'])->name('dashboard.withdraw_requests_widget');
                Route::get('dashboard/loan_requests_widget', [DashboardController::class, 'loan_requests_widget'])->name('dashboard.loan_requests_widget');
                Route::get('dashboard/expense_overview_widget', [DashboardController::class, 'expense_overview_widget'])->name('dashboard.expense_overview_widget');
                Route::get('dashboard/deposit_withdraw_analytics', [DashboardController::class, 'deposit_withdraw_analytics'])->name('dashboard.deposit_withdraw_analytics');
                Route::get('dashboard/recent_transaction_widget', [DashboardController::class, 'recent_transaction_widget'])->name('dashboard.recent_transaction_widget');
                Route::get('dashboard/due_loan_list', [DashboardController::class, 'due_loan_list'])->name('dashboard.due_loan_list');

                Route::prefix('main-services')->middleware(['auth', 'verified'])->group(function () {
                    Route::get('/', [BewinsMainServiceController::class, 'index'])->name('main-services.index');
                    Route::get('/create', [BewinsMainServiceController::class, 'create'])->name('main-services.create');
                    Route::post('/', [BewinsMainServiceController::class, 'store'])->name('main-services.store');
                    Route::get('/{id}/edit', [BewinsMainServiceController::class, 'edit'])->name('main-services.edit');
                    Route::put('/{id}', [BewinsMainServiceController::class, 'update'])->name('main-services.update');
                    //  Route::delete('/{id}', [BewinsMainServiceController::class, 'destroy'])->name('main-services.destroy');
                });
                // Member Controller
                Route::get('commissions/network', [\App\Http\Controllers\CommissionController::class, 'network'])->name('commissions.network');
                Route::get('commissions/settings', [\App\Http\Controllers\CommissionController::class, 'settings'])->name('commissions.settings');
                Route::post('commissions/settings', [\App\Http\Controllers\CommissionController::class, 'updateSettings'])->name('commissions.settings.update');
                Route::get('commissions/history', [\App\Http\Controllers\CommissionController::class, 'history'])->name('commissions.history');
                Route::match(['get', 'post'], 'members/import', [MemberController::class, 'import'])->name('members.import');
                Route::match(['get', 'post'], 'members/accept_request/{id}', [MemberController::class, 'accept_request'])->name('members.accept_request');
                Route::get('members/reject_request/{id}', [MemberController::class, 'reject_request'])->name('members.reject_request');
                Route::get('members/pending_requests', [MemberController::class, 'pending_requests'])->name('members.pending_requests');
                Route::get('members/get_member_transaction_data/{member_id}', [MemberController::class, 'get_member_transaction_data']);
                Route::get('members/get_wallet_transaction_data/{member_id}', [MemberController::class, 'get_wallet_transaction_data']);
                Route::get('members/get_table_data', [MemberController::class, 'get_table_data']);
                Route::post('members/toggle_withdrawal/{id}', [MemberController::class, 'toggle_withdrawal']);
                Route::post('members/send_email', [MemberController::class, 'send_email'])->name('members.send_email');
                Route::post('members/send_sms', [MemberController::class, 'send_sms'])->name('members.send_sms');

                // Member Wallet Routes
                Route::get('members/add_wallet_money/{member_id}', [MemberController::class, 'add_wallet_money'])->name('members.add_wallet_money');
                Route::post('members/store_wallet_money/{member_id}', [MemberController::class, 'store_wallet_money'])->name('members.store_wallet_money');

                //   Route::resource('members', MemberController::class)->middleware("demo:PUT|PATCH|DELETE");

                // Route::get('members', [MemberController::class, 'index']) ->name('members.index')->middleware('demo:PUT|PATCH|DELETE');

                // Show form to create a new member
                Route::get('members', [MemberController::class, 'index'])->name('members.index')->middleware('demo:PUT|PATCH');
                Route::get('members/create', [MemberController::class, 'create'])->name('members.create')->middleware('demo:PUT|PATCH');

                // // Store new member
                Route::post('members', [MemberController::class, 'store'])->name('members.store')->middleware('demo:PUT|PATCH');

                // // Show single member
                Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show')->middleware('demo:PUT|PATCH');

                // // Show form to edit member
                Route::get('members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit')->middleware('demo:PUT|PATCH');

                // // Update member
                Route::put('members/{member}', [MemberController::class, 'update'])->name('members.update')->middleware('demo:PUT|PATCH');

                // //Delete member
                Route::delete('members/{member}', [MemberController::class, 'destroy'])->name('members.destroy')->middleware('demo:PUT|PATCH');

                // Withdrawal Requests
                Route::get('withdrawal-requests', [\App\Http\Controllers\WithdrawalRequestController::class, 'index'])->name('withdrawal_requests.index');
                Route::post('withdrawal-requests/{id}/approve', [\App\Http\Controllers\WithdrawalRequestController::class, 'approve'])->name('withdrawal_requests.approve');
                Route::post('withdrawal-requests/{id}/reject', [\App\Http\Controllers\WithdrawalRequestController::class, 'reject'])->name('withdrawal_requests.reject');

                // Custom Field Controller
                // Route::resource('custom_fields', CustomFieldController::class)->except(['index', 'show'])->middleware("demo");

                // CREATE
                Route::get('custom_fields/create', [CustomFieldController::class, 'create'])
                    ->name('custom_fields.create')
                    ->middleware('demo');

                // STORE
                Route::post('custom_fields', [CustomFieldController::class, 'store'])
                    ->name('custom_fields.store')
                    ->middleware('demo');

                // EDIT
                Route::get('custom_fields/{custom_field}/edit', [CustomFieldController::class, 'edit'])
                    ->name('custom_fields.edit')
                    ->middleware('demo');

                // UPDATE
                Route::put('custom_fields/{custom_field}', [CustomFieldController::class, 'update'])
                    ->name('custom_fields.update')
                    ->middleware('demo');

                Route::get('custom_fields/{table}', [CustomFieldController::class, 'index'])->name('custom_fields.index');

                // Members Documents
                Route::get('member_documents/{member_id}', [MemberDocumentController::class, 'index'])->name('member_documents.index');
                Route::get('member_documents/create/{member_id}', [MemberDocumentController::class, 'create'])->name('member_documents.create');

                // Route::resource('member_documents', MemberDocumentController::class)->except(['index', 'create', 'show']);

                // STORE
                Route::post('member_documents', [MemberDocumentController::class, 'store'])
                    ->name('member_documents.store');

                // EDIT
                Route::get('member_documents/{member_document}/edit', [MemberDocumentController::class, 'edit'])
                    ->name('member_documents.edit');

                // UPDATE
                Route::put('member_documents/{member_document}', [MemberDocumentController::class, 'update'])
                    ->name('member_documents.update');

                // DESTROY
                // Route::delete('member_documents/{member_document}', [MemberDocumentController::class, 'destroy'])
                //     ->name('member_documents.destroy');

                // Savings Accounts
                Route::get('savings_accounts/get_account_by_member_id/{member_id}', [SavingsAccountController::class, 'get_account_by_member_id']);
                Route::get('savings_accounts/get_table_data', [SavingsAccountController::class, 'get_table_data']);

                // Route::resource('savings_accounts', SavingsAccountController::class)->middleware("demo:PUT|PATCH|DELETE");

                // INDEX
                Route::get('savings_accounts', [SavingsAccountController::class, 'index'])
                    ->name('savings_accounts.index');

                // CREATE
                Route::get('savings_accounts/create', [SavingsAccountController::class, 'create'])
                    ->name('savings_accounts.create');

                // STORE
                Route::post('savings_accounts', [SavingsAccountController::class, 'store'])
                    ->name('savings_accounts.store');

                // SHOW
                Route::get('savings_accounts/{savings_account}', [SavingsAccountController::class, 'show'])
                    ->name('savings_accounts.show');

                // EDIT
                Route::get('savings_accounts/{savings_account}/edit', [SavingsAccountController::class, 'edit'])
                    ->name('savings_accounts.edit');

                // UPDATE (with demo middleware)
                Route::match(['put', 'patch'], 'savings_accounts/{savings_account}', [SavingsAccountController::class, 'update'])
                    ->middleware('demo')
                    ->name('savings_accounts.update');

                // DESTROY (with demo middleware)
                // Route::delete('savings_accounts/{savings_account}', [SavingsAccountController::class, 'destroy'])
                //     ->middleware('demo')
                //     ->name('savings_accounts.destroy');

                // Interest Controller
                Route::get('interest_calculation/get_last_posting/{account_type_id?}', [InterestController::class, 'get_last_posting'])->name('interest_calculation.get_last_posting');
                Route::match(['get', 'post'], 'interest_calculation/calculator', [InterestController::class, 'calculator'])->name('interest_calculation.calculator');
                Route::post('interest_calculation/posting', [InterestController::class, 'interest_posting'])->name('interest_calculation.interest_posting');

                // Transaction
                Route::get('transactions/get_table_data', [TransactionController::class, 'get_table_data']);
                // Route::resource('transactions', TransactionController::class);

                // INDEX
                Route::get('transactions', [TransactionController::class, 'index'])
                    ->name('transactions.index');

                // CREATE
                Route::get('transactions/create', [TransactionController::class, 'create'])
                    ->name('transactions.create');

                // STORE
                Route::post('transactions', [TransactionController::class, 'store'])
                    ->name('transactions.store');

                // SHOW
                Route::get('transactions/{transaction}', [TransactionController::class, 'show'])
                    ->name('transactions.show');

                // EDIT
                Route::get('transactions/{transaction}/edit', [TransactionController::class, 'edit'])
                    ->name('transactions.edit');

                // UPDATE
                Route::match(['put', 'patch'], 'transactions/{transaction}', [TransactionController::class, 'update'])
                    ->name('transactions.update');

                // DESTROY
                // Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])
                //     ->name('transactions.destroy');

                // Get Transaction Categories
                Route::get('transaction_categories/get_category_by_type/{type}', [TransactionCategoryController::class, 'get_category_by_type']);

                // Deposit Requests
                Route::post('deposit_requests/get_table_data', [DepositRequestController::class, 'get_table_data']);
                Route::get('deposit_requests/approve/{id}', [DepositRequestController::class, 'approve'])->name('deposit_requests.approve');
                Route::get('deposit_requests/reject/{id}', [DepositRequestController::class, 'reject'])->name('deposit_requests.reject');
                // Route::delete('deposit_requests/{id}', [DepositRequestController::class, 'destroy'])->name('deposit_requests.destroy');
                Route::get('deposit_requests/{id}', [DepositRequestController::class, 'show'])->name('deposit_requests.show');
                Route::get('deposit_requests', [DepositRequestController::class, 'index'])->name('deposit_requests.index');

                // Withdraw Requests
                Route::post('withdraw_requests/get_table_data', [WithdrawRequestController::class, 'get_table_data']);
                Route::get('withdraw_requests/approve/{id}', [WithdrawRequestController::class, 'approve'])->name('withdraw_requests.approve');
                Route::get('withdraw_requests/reject/{id}', [WithdrawRequestController::class, 'reject'])->name('withdraw_requests.reject');
                // Route::delete('withdraw_requests/{id}', [WithdrawRequestController::class, 'destroy'])->name('withdraw_requests.destroy');
                Route::get('withdraw_requests/{id}', [WithdrawRequestController::class, 'show'])->name('withdraw_requests.show');
                Route::get('withdraw_requests', [WithdrawRequestController::class, 'index'])->name('withdraw_requests.index');

                // Expense
                Route::get('expenses/get_table_data', [ExpenseController::class, 'get_table_data']);
                // Route::resource('expenses', ExpenseController::class);

                // INDEX
                Route::get('expenses', [ExpenseController::class, 'index'])
                    ->name('expenses.index');

                // CREATE
                Route::get('expenses/create', [ExpenseController::class, 'create'])
                    ->name('expenses.create');

                // STORE
                Route::post('expenses', [ExpenseController::class, 'store'])
                    ->name('expenses.store');

                // SHOW
                Route::get('expenses/{expense}', [ExpenseController::class, 'show'])
                    ->name('expenses.show');

                // EDIT
                Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])
                    ->name('expenses.edit');

                // UPDATE
                Route::match(['put', 'patch'], 'expenses/{expense}', [ExpenseController::class, 'update'])
                    ->name('expenses.update');

                // DESTROY
                // Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])
                //     ->name('expenses.destroy');

                // Loan Controller
                Route::post('loans/get_table_data', [LoanController::class, 'get_table_data']);
                Route::get('loans/calculator', [LoanController::class, 'calculator'])->name('loans.admin_calculator');
                Route::post('loans/calculator/calculate', [LoanController::class, 'calculate'])->name('loans.calculate');
                Route::get('loans/approve/{id}', [LoanController::class, 'approve'])->name('loans.approve');
                Route::get('loans/reject/{id}', [LoanController::class, 'reject'])->name('loans.reject');
                Route::get('loans/filter/{status?}', [LoanController::class, 'index'])->name('loans.filter')->where('status', '[A-Za-z]+');


                // Disbursement Queue
                Route::get('loans/disbursement-queue', [LoanController::class, 'disbursementQueue'])->name('loans.disbursement_queue');

                Route::post('loans/{id}/record-disbursement', [LoanController::class, 'recordDisbursement'])->whereNumber('id')->name('loans.record_disbursement');

                // Activity History / Audit Trail. Keep this before loans/{loan}
                // so "activity-logs" is not treated as a loan ID.
                Route::get('loans/activity-logs', [ActivityLogController::class, 'index'])
                    ->name('admin.activity_logs.index');
                // Preserve the previous URL for existing bookmarks.
                Route::get('activity-logs', [ActivityLogController::class, 'index']);

                // Overdue Loans
                Route::get('loans/overdue', [LoanController::class, 'overdue'])->name('loans.overdue');
                Route::post('loans/overdue/get_table_data', [LoanController::class, 'get_overdue_table_data'])->name('loans.overdue.table_data');

                // INDEX
                Route::get('loans', [LoanController::class, 'index'])
                    ->name('loans.index');

                // CREATE
                Route::get('loans/create', [LoanController::class, 'create'])
                    ->name('loans.create');

                // STORE
                Route::post('loans', [LoanController::class, 'store'])
                    ->name('loans.store');

                // SHOW
                Route::get('loans/{loan}', [LoanController::class, 'show'])
                    ->name('loans.show');

                // EDIT
                Route::get('loans/{loan}/edit', [LoanController::class, 'edit'])
                    ->name('loans.edit');

                // UPDATE
                Route::match(['put', 'patch'], 'loans/{loan}', [LoanController::class, 'update'])
                    ->name('loans.update');

                // DESTROY
                // Route::delete('loans/{loan}', [LoanController::class, 'destroy'])
                //     ->name('loans.destroy');

                // Loan Collateral Controller
                Route::get('loan_collaterals/loan/{loan_id}', [LoanCollateralController::class, 'index'])->name('loan_collaterals.index');
                // Route::resource('loan_collaterals', LoanCollateralController::class)->except('index');
                // CREATE
                Route::get('loan_collaterals/create', [LoanCollateralController::class, 'create'])
                    ->name('loan_collaterals.create');

                // STORE
                Route::post('loan_collaterals', [LoanCollateralController::class, 'store'])
                    ->name('loan_collaterals.store');

                // SHOW
                Route::get('loan_collaterals/{loan_collateral}', [LoanCollateralController::class, 'show'])
                    ->name('loan_collaterals.show');

                // EDIT
                Route::get('loan_collaterals/{loan_collateral}/edit', [LoanCollateralController::class, 'edit'])
                    ->name('loan_collaterals.edit');

                // UPDATE
                Route::match(['put', 'patch'], 'loan_collaterals/{loan_collateral}', [LoanCollateralController::class, 'update'])
                    ->name('loan_collaterals.update');

                // DESTROY
                // Route::delete('loan_collaterals/{loan_collateral}', [LoanCollateralController::class, 'destroy'])
                //     ->name('loan_collaterals.destroy');

                // Loan Guarantor Controller
                // Route::resource('guarantors', GuarantorController::class)->except(['show', 'index']);

                // CREATE
                Route::get('guarantors/create', [GuarantorController::class, 'create'])
                    ->name('guarantors.create');

                // STORE
                Route::post('guarantors', [GuarantorController::class, 'store'])
                    ->name('guarantors.store');

                // EDIT
                Route::get('guarantors/{guarantor}/edit', [GuarantorController::class, 'edit'])
                    ->name('guarantors.edit');

                // UPDATE
                Route::match(['put', 'patch'], 'guarantors/{guarantor}', [GuarantorController::class, 'update'])
                    ->name('guarantors.update');

                // DESTROY
                // Route::delete('guarantors/{guarantor}', [GuarantorController::class, 'destroy'])
                //     ->name('guarantors.destroy');

                // Loan Payment Controller
                Route::get('loan_payments/get_repayment_by_loan_id/{loan_id}', [LoanPaymentController::class, 'get_repayment_by_loan_id']);
                Route::get('loan_payments/get_table_data', [LoanPaymentController::class, 'get_table_data']);
                // Route::resource('loan_payments', LoanPaymentController::class);
                // INDEX
                Route::get('loan_payments', [LoanPaymentController::class, 'index'])
                    ->name('loan_payments.index');

                // CREATE
                Route::get('loan_payments/create', [LoanPaymentController::class, 'create'])
                    ->name('loan_payments.create');

                // STORE
                Route::post('loan_payments', [LoanPaymentController::class, 'store'])
                    ->name('loan_payments.store');

                // SHOW
                Route::get('loan_payments/{loan_payment}', [LoanPaymentController::class, 'show'])
                    ->name('loan_payments.show');

                // EDIT
                Route::get('loan_payments/{loan_payment}/edit', [LoanPaymentController::class, 'edit'])
                    ->name('loan_payments.edit');

                // UPDATE
                Route::match(['put', 'patch'], 'loan_payments/{loan_payment}', [LoanPaymentController::class, 'update'])
                    ->name('loan_payments.update');

                // DESTROY
                // Route::delete('loan_payments/{loan_payment}', [LoanPaymentController::class, 'destroy'])
                //     ->name('loan_payments.destroy');

                // Report Controller
                Route::match(['get', 'post'], 'reports/account_statement', [ReportController::class, 'account_statement'])->name('reports.account_statement');
                Route::match(['get', 'post'], 'reports/account_balances', [ReportController::class, 'account_balances'])->name('reports.account_balances');
                Route::match(['get', 'post'], 'reports/transactions_report', [ReportController::class, 'transactions_report'])->name('reports.transactions_report');
                Route::match(['get', 'post'], 'reports/loan_report', [ReportController::class, 'loan_report'])->name('reports.loan_report');
                Route::get('reports/loan_due_report', [ReportController::class, 'loan_due_report'])->name('reports.loan_due_report');
                Route::match(['get', 'post'], 'reports/expense_report', [ReportController::class, 'expense_report'])->name('reports.expense_report');
                Route::match(['get', 'post'], 'reports/revenue_report', [ReportController::class, 'revenue_report'])->name('reports.revenue_report');

                // Wallet Routes

                Route::get('wallets', 'Admin\WalletController@index')->name('admin.wallets.index');

                Route::post('wallets/add-money', 'Admin\WalletController@addMoney')->name('admin.wallets.add-money');

                Route::get('/wallet/user-details/{id}', [App\Http\Controllers\Admin\WalletController::class, 'getUserDetails'])->name('admin.wallet.userDetails');

                // manish 11/20/2025
                Route::get('admin/wallet/user-account-details/{id}', [WalletController::class, 'getUserAccountDetails'])->name('admin.wallet.userAccountDetails');

                Route::post('/wallet/pay-cod/{id}', [WalletController::class, 'payCOD'])->name('admin.wallet.payCOD');

                Route::get('wallets/history', 'Admin\WalletController@history')->name('admin.wallets.history');
                // Route::get('wallets/history/id', 'Admin\WalletController@history')->name('admin.wallets.history');

                // Member PAN Verification
                Route::post('members/verify-pan', [MemberController::class, 'verifyPan'])->name('members.verify-pan');
            });

            // Personal activity is available to any authenticated account; the
            // controller limits the results to the signed-in user.
            Route::get('my-activity', [ActivityLogController::class, 'myActivity'])
                ->name('my_activity.index');

            Route::group(['middleware' => ['customer'], 'prefix' => 'portal'], function () {

                // Membership Details
                Route::get('profile/membership_details', [ProfileController::class, 'membership_details'])->name('profile.membership_details');

                // Transfer Controller
                Route::match(['get', 'post'], 'transfer/own_account_transfer', [App\Http\Controllers\Customer\TransferController::class, 'own_account_transfer'])->name('transfer.own_account_transfer');
                Route::match(['get', 'post'], 'transfer/other_account_transfer', [App\Http\Controllers\Customer\TransferController::class, 'other_account_transfer'])->name('transfer.other_account_transfer');
                Route::get('transfer/transaction_details/{id}', [App\Http\Controllers\Customer\TransferController::class, 'transaction_details'])->name('trasnactions.details');
                Route::get('transfer/get_exchange_amount/{from?}/{to?}/{amount?}', [App\Http\Controllers\Customer\TransferController::class, 'get_exchange_amount'])->name('transfer.get_exchange_amount');
                Route::post('transfer/get_final_amount', [App\Http\Controllers\Customer\TransferController::class, 'get_final_amount'])->name('transfer.get_final_amount');
                Route::get('transfer/transaction_requests', [App\Http\Controllers\Customer\TransferController::class, 'transaction_requests'])->name('trasnactions.transaction_requests');

                // Loan Controller
                Route::match(['get', 'post'], 'loans/calculator', [App\Http\Controllers\Customer\LoanController::class, 'calculator'])->name('loans.calculator');
                Route::match(['get', 'post'], 'loans/apply_loan', [App\Http\Controllers\Customer\LoanController::class, 'apply_loan'])->name('loans.apply_loan');
                Route::get('loans/loan_details/{id}', [App\Http\Controllers\Customer\LoanController::class, 'loan_details'])->name('loans.loan_details');
                Route::match(['get', 'post'], 'loans/payment/{loan_id}', [App\Http\Controllers\Customer\LoanController::class, 'loan_payment'])->name('loans.loan_payment');
                Route::get('loans/my_loans', [App\Http\Controllers\Customer\LoanController::class, 'index'])->name('loans.my_loans');

                // Deposit Money
                Route::match(['get', 'post'], 'deposit/manual_deposit/{id}', [App\Http\Controllers\Customer\DepositController::class, 'manual_deposit'])->name('deposit.manual_deposit');
                Route::get('deposit/manual_methods', [App\Http\Controllers\Customer\DepositController::class, 'manual_methods'])->name('deposit.manual_methods');

                // Automatic Deposit
                Route::get('deposit/get_exchange_amount/{from?}/{to?}/{amount?}', [App\Http\Controllers\Customer\DepositController::class, 'get_exchange_amount'])->name('deposit.get_exchange_amount');
                Route::match(['get', 'post'], 'deposit/automatic_deposit/{id}', [App\Http\Controllers\Customer\DepositController::class, 'automatic_deposit'])->name('deposit.automatic_deposit');
                Route::get('deposit/automatic_methods', [App\Http\Controllers\Customer\DepositController::class, 'automatic_methods'])->name('deposit.automatic_methods');

                // Withdraw Money
                Route::match(['get', 'post'], 'withdraw/manual_withdraw/{id}/{otp?}', [App\Http\Controllers\Customer\WithdrawController::class, 'manual_withdraw'])->name('withdraw.manual_withdraw');
                Route::get('withdraw/manual_methods', [App\Http\Controllers\Customer\WithdrawController::class, 'manual_methods'])->name('withdraw.manual_methods');

                // Report Controller
                Route::match(['get', 'post'], 'reports/account_statement', [App\Http\Controllers\Customer\ReportController::class, 'account_statement'])->name('customer_reports.account_statement');
                Route::match(['get', 'post'], 'reports/transactions_report', [App\Http\Controllers\Customer\ReportController::class, 'transactions_report'])->name('customer_reports.transactions_report');
                Route::match(['get', 'post'], 'reports/account_balances', [App\Http\Controllers\Customer\ReportController::class, 'account_balances'])->name('customer_reports.account_balances');
            });

            Route::get('switch_language/', function () {
                if (isset($_GET['language'])) {
                    session(['language' => $_GET['language']]);
                    return back();
                }
            })->name('switch_language');

            Route::get('switch_branch/', function () {
                if (isset($_GET['branch']) && isset($_GET['branch_id'])) {
                    session(['branch' => $_GET['branch'], 'branch_id' => $_GET['branch_id']]);
                } else {
                    request()->session()->forget(['branch', 'branch_id']);
                }
                return back();
            })->name('switch_branch');
        });
    });
});

Route::namespace('Gateway')->prefix('callback')->name('callback.')->group(function () {
    // Fiat Currency
    Route::get('paypal', 'PayPal\ProcessController@callback')->name('PayPal')->middleware('auth');
    Route::post('stripe', 'Stripe\ProcessController@callback')->name('Stripe')->middleware('auth');
    Route::post('razorpay', 'Razorpay\ProcessController@callback')->name('Razorpay')->middleware('auth');
    Route::get('paystack', 'Paystack\ProcessController@callback')->name('Paystack')->middleware('auth');
    Route::get('flutterwave', 'Flutterwave\ProcessController@callback')->name('Flutterwave')->middleware('auth');
    Route::match(['get', 'post'], 'voguepay', 'VoguePay\ProcessController@callback')->name('VoguePay');
    Route::get('mollie', 'Mollie\ProcessController@callback')->name('Mollie')->middleware('auth');
    Route::match(['get', 'post'], 'instamojo', 'Instamojo\ProcessController@callback')->name('Instamojo');

    // Crypto Currency
    Route::get('blockchain', 'BlockChain\ProcessController@callback')->name('BlockChain');
    Route::post('coinpayments', 'CoinPayments\ProcessController@callback')->name('CoinPayments');
});

Route::get('dashboard/json_expense_by_category', [DashboardController::class, 'json_expense_by_category'])->middleware('auth');
Route::get('dashboard/json_deposit_withdraw_analytics/{currency_id?}', [DashboardController::class, 'json_deposit_withdraw_analytics'])->middleware('auth');

// Social Login
Route::get('/login/{provider}', [SocialController::class, 'redirect']);
Route::get('/login/{provider}/callback', [SocialController::class, 'callback']);

// Ajax Select2 Controller
Route::get('ajax/get_table_data', [Select2Controller::class, 'get_table_data']);

Route::get('/installation', 'Install\InstallController@index');
Route::get('install/database', 'Install\InstallController@database');
Route::post('install/process_install', 'Install\InstallController@process_install');
Route::get('install/create_user', 'Install\InstallController@create_user');
Route::post('install/store_user', 'Install\InstallController@store_user');
Route::get('install/system_settings', 'Install\InstallController@system_settings');
Route::post('install/finish', 'Install\InstallController@final_touch');

// Update System
Route::get('migration/update', 'Install\UpdateController@update_migration');

Route::post('roles/get_role_name', 'RoleController@getRoleName');

// Add the my-agents route here

Route::get('users/my-agents', [UserController::class, 'myAgents'])->name('users.my_agents')->middleware(['auth', 'verified']);

// Dashboard Widget

// ... existing code ...

Route::post('/update-order-status/{id}', [OrderPlacedController::class, 'updateStatus']);
Route::post('/orders/document-status-update', [OrderPlacedController::class, 'updateDocumentStatus'])->name('orders.document.status.update');
Route::get('/order_edit/{id}', [OrderPlacedController::class, 'editshow'])->name('edit.show');

Route::post('/order/{id}/document', [OrderPlacedController::class, 'storeDocument'])->name('order.document.store');
Route::get('/orders/{id}/documents', [OrderPlacedController::class, 'getDocuments'])->name('documents.get');
Route::get('/user_profile', [userController::class, 'userProfile']);

Route::prefix('services')->middleware(['auth', 'verified'])->group(function () {
    Route::get('{category}/{subCategory?}', [MainServiceController::class, 'index'])->name('services.index');
    Route::get('{category}/{subCategory?}/create', [MainServiceController::class, 'create'])->name('services.create');
    Route::post('{category}/{subCategory?}', [MainServiceController::class, 'store'])->name('services.store');
    Route::get('{category}/{subCategory?}/show/{id}', [MainServiceController::class, 'show'])->name('services.show');
    Route::post('{category}/{subCategory?}/order', [MainServiceController::class, 'storeOrder'])->name('services.order');
});

Route::get('/agreement/view', [AgreementController::class, 'show'])->name('agreement.view');
Route::get('/certificate/view', [AgreementController::class, 'showCertificate'])->name('certificate.view');
Route::get('/loan-agreement/view', [AgreementController::class, 'showLoanAgreement'])->name('loan_agreement.view');
Route::get('/orders/{id}/invoice', [OrderPlacedController::class, 'invoice'])->name('orders.invoice');

Route::view('/vedio', 'vedio.vedio')->name('bewins.vedio');

Route::get('/email-notifications', [EmailNotificationController::class, 'index'])->name('emails.index');
Route::post('/email-notifications/send', [EmailNotificationController::class, 'send'])->name('email_notifications.send');

Route::middleware(['auth'])->group(function () {
    Route::get('/my-referrals', [\App\Http\Controllers\CommissionController::class, 'myReferrals'])->name('commissions.mine');
    Route::get('/fd', [FDController::class, 'index'])->name('fd.index');
    Route::get('/fd/create', [FDController::class, 'create'])->name('fd.create');
    Route::post('/fd/store', [FDController::class, 'store'])->name('fd.store');
    Route::get('/fd/{id}', [FDController::class, 'show'])->name('fd.show');
    Route::get('fd/edit/{id}', [FDController::class, 'edit'])->name('fd.edit');
    Route::put('fd/update/{id}', [FDController::class, 'update'])->name('fd.update');
    // Route::post('/fd/{id}/approve', [FDController::class, 'approve'])->name('fd.approve'); // admin only
    // Route::post('/fd/{id}/withdraw', [FDController::class, 'withdraw'])->name('fd.withdraw');
});

// invest

// Investment Routes
Route::get('investments', [InvestmentController::class, 'index'])->name('investments.index');
Route::get('investments/create', [InvestmentController::class, 'create'])->name('investments.create');
Route::post('investments', [InvestmentController::class, 'store'])->name('investments.store');
Route::get('investments/{investment}', [InvestmentController::class, 'show'])->name('investments.show');
Route::get('investments/{investment}/edit', [InvestmentController::class, 'edit'])->name('investments.edit');
Route::put('investments/{investment}', [InvestmentController::class, 'update'])->name('investments.update');
Route::delete('investments/{investment}', [InvestmentController::class, 'destroy'])->name('investments.destroy');

Route::prefix('loans')->group(function () {
    Route::get('{loanId}/agreement/create', [LoanAgreementController::class, 'create'])->name('loan.agreement.create');
    Route::post('{loanId}/agreement/store', [LoanAgreementController::class, 'store'])->name('loan.agreement.store');
});

// invest
Route::get('investment/agreement/create/{id}', [InvestmentAgreementController::class, 'create'])->name('investment.agreement.create');
Route::post('investment/agreement/store/{id}', [InvestmentAgreementController::class, 'store'])->name('investment.agreement.store');

// FD
Route::get('fd/agreement/create/{id}', [FdAgreementController::class, 'create'])->name('fd.agreement.create');
Route::post('fd/agreement/store/{id}', [FdAgreementController::class, 'store'])->name('fd.agreement.store');

Route::post('/sendrequest_withdraw', [AdminWithdrawRequestController::class, 'send'])->name('sendrequest.store');
Route::get('/getrequest_withdraw', [AdminWithdrawRequestController::class, 'index'])->name('getrequest.index');

Route::post('/withdraw/change-status', [AdminWithdrawRequestController::class, 'changeStatus'])
    ->name('withdraw.changeStatus');

Route::post('/withdraw/update', [AdminWithdrawRequestController::class, 'update'])
    ->name('withdraw.update');

Route::get('/withdraw/approved', [AdminWithdrawRequestController::class, 'approvedlist'])
    ->name('withdraw.approvedlist');

// loan
Route::get('/loan/repayment/details/{id}', [LoanController::class, 'getDetails']);
Route::post('/loan/repayment/pay', [LoanController::class, 'pay']);

// Investment repayment routes
Route::get('/investment/repayment/details/{id}', [InvestmentController::class, 'getRepaymentDetails']);

Route::post('/investment/repayment/pay', [InvestmentController::class, 'payRepayment']);

// Cash Collection Tracker Routes
Route::get('admin/investments/cash-collection', [InvestmentController::class, 'cashCollectionTracker'])->name('investments.cash_collection');
Route::post('admin/investments/verify-cash/{id}', [InvestmentController::class, 'verifyCashCollection'])->name('investments.verify_cash');
Route::get('admin/investments/download-receipt/{id}', [InvestmentController::class, 'downloadReceipt'])->name('investments.download_receipt');
Route::post('admin/investments/toggle-lock/{id}', [InvestmentController::class, 'toggleLock'])
    ->name('investments.toggle_lock')->middleware(['auth', 'verified']);
Route::get('admin/investments/closure-certificate/{id}', [InvestmentController::class, 'downloadClosureCertificate'])
    ->name('investments.closure_certificate.download')->middleware(['auth', 'verified']);
Route::post('admin/investments/closure-certificate/{id}/resend', [InvestmentController::class, 'resendClosureCertificate'])
    ->name('investments.closure_certificate.resend')->middleware(['auth', 'verified']);


// master expected return
Route::get('/investment/expected-return/index', [InvestmentExpectedReturnController::class, 'index'])->name('investment.expected-return.index');
Route::get('/investment/expected-return/create', [InvestmentExpectedReturnController::class, 'create'])->name('investment.expected-return.create');
Route::post('/investment/expected-return/store', [InvestmentExpectedReturnController::class, 'store'])->name('investment.expected-return.store');

Route::get('/investment/expected-return/edit/{id}', [InvestmentExpectedReturnController::class, 'edit'])->name('investment.expected-return.edit');
Route::put('/investment/expected-return/update/{id}', [InvestmentExpectedReturnController::class, 'update'])->name('investment.expected-return.update');
Route::delete('/investment/expected-return/destroy/{id}', [InvestmentExpectedReturnController::class, 'destroy'])->name('investment.expected-return.destroy');
Route::get('/investment/expected-return/toggle-status/{id}', [InvestmentExpectedReturnController::class, 'toggleStatus'])->name('investment.expected-return.toggle-status');

// FD
Route::get('/fd/repayment/details/{id}', [FdController::class, 'getDetails']);
Route::post('/fd/repayment/pay', [FdController::class, 'payRepayment']);

Route::get('/fd/approve/{id}', [FDController::class, 'approve'])->name('fd.approve');
Route::get('/fd/reject/{id}', [FDController::class, 'reject'])->name('fd.reject');
Route::get('/investments/approve/{id}', [InvestmentController::class, 'approve'])->name('investment.approve');
Route::get('/investments/reject/{id}', [InvestmentController::class, 'reject'])->name('investment.reject');

// Razorpay Webhook for Smart Collect (Virtual Accounts)
Route::post('/webhook/razorpay', [WalletController::class, 'handleWebhook'])->name('razorpay.webhook');

// Test Webhook (Development Only - Remove in Production)
// Route::get('/test/webhook/razorpay', [WalletController::class, 'testWebhook'])->name('razorpay.webhook.test');
