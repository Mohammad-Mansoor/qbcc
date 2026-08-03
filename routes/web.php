<?php

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
use App\Activity;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


/** Super Admin Routes */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_activities']], function () {
    Route::get('activities', 'ActivityController@index');
    Route::post('search_activities', 'ActivityController@search_activities');
});
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:delete_activities']], function () {
    Route::delete('activities/{a_id}', 'ActivityController@destroy');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_users']], function () {
    Route::resource('/users', 'UserController');
    Route::post('/user-search', 'UserController@search');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:manage_roles_and_permissions']], function () {
    Route::resource('/roles', 'RoleController');
});
    // Route::get('/edit-currency/{id}', 'DashboardController@editCurrency');
    // Route::put('/update-currency/{id}', 'DashboardController@updateCurrency');

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_currencies']], function () {
    Route::resource('/currencies', 'Accounting\CurrencyController')->names([
        'index' => 'accounting.currencies.index',
        'create' => 'accounting.currencies.create',
        'store' => 'accounting.currencies.store',
        'edit' => 'accounting.currencies.edit',
        'update' => 'accounting.currencies.update',
        'destroy' => 'accounting.currencies.destroy',
    ]);
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_buy_carpets']], function () {
    /** route for all carpets list */
    Route::get('/all-carpets', 'CarpetsController@all_carpets');
    Route::post('/view-carpets/search', 'CarpetsController@all_carpets_search');
    Route::get('/view-carpets', 'CarpetsController@all_carpets_view');

    /** route for filter based on carpet type */

    Route::post('filter-based-carpet-type', 'CarpetsController@filter_based_carpet_type');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_agent_statement']], function () {
    Route::get('talab-mardom/{type?}', 'DashboardController@talab_mardom');
    Route::get('qarz-mardom/{type?}', 'DashboardController@qarz_mardom');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:approve_agent_money_requests']], function () {
    Route::get('money-request-list', 'OfficeCreditController@money_request');
    Route::delete('approve-request-money/{id}', 'OfficeCreditController@approve_request');
    Route::delete('delete-request-money/{id}', 'OfficeCreditController@delete_request');    /** agent money request list  */

    Route::delete('agent-approve-request-money/{id}', 'AgentPaymentController@approve_request');
    Route::delete('agent-delete-request-money/{id}', 'AgentPaymentController@delete_request');

    /** end agent money request list */
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_agent_money_requests']], function () {
    Route::get('agent-money-request-list', 'AgentPaymentController@money_request');
});
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_different_account_money_requests']], function () {
    Route::get('different-account-money-request-list', 'DifferentAccountPaymentController@money_request');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:approve_different_account_money_requests']], function () {
    Route::delete('different-account-approve-request-money/{id}', 'DifferentAccountPaymentController@approve_request');
    Route::delete('different-account-delete-request-money/{id}', 'DifferentAccountPaymentController@delete_request');
});
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_different_account_money_requests']], function () {
    Route::get('new-different-account-money-request-list', 'NewDifferentAccountPaymentController@money_request');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:approve_different_account_money_requests']], function () {
    Route::delete('new-different-account-approve-request-money/{id}', 'NewDifferentAccountPaymentController@approve_request');
    Route::delete('new-different-account-delete-request-money/{id}', 'NewDifferentAccountPaymentController@delete_request');
});
    
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_kachaee_money_requests']], function () {
    Route::get('kachaee-money-request-list', 'KachaeePaymentController@money_request');
    Route::delete('kachaee-approve-request-money/{id}', 'KachaeePaymentController@approve_request')->middleware('permission:approve_kachaee_money_requests');
    Route::delete('kachaee-delete-request-money/{id}', 'KachaeePaymentController@delete_request')->middleware('permission:reject_kachaee_money_requests');
});
    
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_washing_money_requests']], function () {
    Route::get('washing-money-request-list', 'WashingPaymentController@money_request');
    Route::delete('washing-approve-request-money/{id}', 'WashingPaymentController@approve_request')->middleware('permission:approve_washing_money_requests');
    Route::delete('washing-delete-request-money/{id}', 'WashingPaymentController@delete_request')->middleware('permission:reject_washing_money_requests');
});
    

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_finishing_money_requests']], function () {
    Route::get('finishing-money-request-list', 'FinishingTeamPaymentController@money_request');
    Route::delete('finishing-approve-request-money/{id}', 'FinishingTeamPaymentController@approve_request')->middleware('permission:approve_finishing_money_requests');
    Route::delete('finishing-delete-request-money/{id}', 'FinishingTeamPaymentController@delete_request')->middleware('permission:reject_finishing_money_requests');
});
    
    
    
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_purchase_material_requests']], function () {
    Route::get('purchase-material-request-list', 'PurchaseMaterialController@request_list');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:approve_purchase_material_requests']], function () {
    Route::delete('purchase-material-approve-request/{id}', 'PurchaseMaterialController@approve_request');
    Route::delete('purchase-material-delete-request/{id}', 'PurchaseMaterialController@delete_request');
});
    
    
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_material_sale_requests']], function () {
    Route::get('material-sales/request-list', 'MaterialSaleController@request_list');
    Route::get('material-sale-request-list', 'MaterialSaleController@request_list');   // alias (hyphenated form)
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:approve_material_sale_requests']], function () {
    Route::delete('material-sales/approve-request/{id}', 'MaterialSaleController@approve_request');
    Route::delete('material-sale-approve-request/{id}', 'MaterialSaleController@approve_request');  // alias
    Route::delete('material-sales/delete-request/{id}', 'MaterialSaleController@delete_request');
    Route::delete('material-sale-delete-request/{id}', 'MaterialSaleController@delete_request');    // alias
});
    
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_seller_money_requests']], function () {
    Route::get('string-seller-request-list', 'SellerPaymentController@request_list');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:approve_seller_money_requests']], function () {
    Route::delete('string-seller-approve-request/{id}', 'SellerPaymentController@approve_request');
    Route::delete('string-seller-delete-request/{id}', 'SellerPaymentController@delete_request');
});
    
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_employee_money_requests']], function () {
    Route::get('employee-request-list', 'EmployeePaymentController@request_list');
    Route::delete('employee-approve-request/{id}', 'EmployeePaymentController@approve_request')->middleware('permission:approve_employee_money_requests');
    Route::delete('employee-delete-request/{id}', 'EmployeePaymentController@delete_request')->middleware('permission:reject_employee_money_requests');
});


Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_customer_money_requests']], function () {
    Route::get('customer-request-list', 'CustomerPaymentController@request_list');
    Route::delete('customer-approve-request/{id}', 'CustomerPaymentController@approve_request')->middleware('permission:approve_customer_money_requests');
    Route::delete('customer-delete-request/{id}', 'CustomerPaymentController@delete_request')->middleware('permission:reject_customer_money_requests');
});



Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_agent_statement']], function () {
    Route::get('/agent_balance_report','ReportController@agent_balance_report');
    Route::any('/get_agent_balance_report','ReportController@get_agent_balance_report');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_monthly_expenses']], function () {
    Route::get('/expense_report','ReportController@expense_report');
    Route::any('/get_expense_report','ReportController@get_expense_report');
});

/** End super admin route */

/** Sales and Finishing Center Routes */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_carpet_stock']], function () {
    /** Route For Carpet Stock  */
    Route::get('/carpet-stock', 'CarpetsController@carpet_stock');

    /** route for carpet stock filter based on type */
    Route::match(['get', 'post'], '/filter-ba-asas-type', 'CarpetsController@filter_ba_asas_type');

    /** Route For Carpet Search date range */
    Route::match(['get', 'post'], '/carpet-stock/search-date-range', 'CarpetsController@stock_search_date_range');
    /** Route For Carpet Stock Search */
    Route::match(['get', 'post'], '/carpet-stock/search', 'CarpetsController@carpet_stock_search');

    /** Route For Carpet Stock Details  */
    Route::get('/carpet-stock-details/{carpet_id}', 'CarpetsController@carpet_stock_details')->middleware('permission:view_carpet_stock_details');
    Route::post('/carpet-stock/update-dimensions', 'CarpetsController@update_dimensions')->middleware('permission:edit_carpet_dimensions');
    Route::get('carpets-in-sales-office', 'CarpetsController@carpets_in_sales_office');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_finishing_teams']], function () {
    // Route for finishing team Category
//    Route::resource('/finish-team-category', 'FinishingTeamCategoryController')->parameters(['finish-team-category' => 'category']);
    // Route for finishing team
    Route::resource('/finish-team', 'FinishingTeamController')->parameters(['finish-team' => 'team']);
    Route::post('/finish-team/search', 'FinishingTeamController@search');
    Route::get('/finishing-accounts', 'FinishingTeamController@accounts');
    Route::post('/finish-team/note/{id}', 'FinishingTeamController@updateNote')->name('finish_team.update_note');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_finishing_centers']], function () {
    // FINISHING CENTER
    Route::resource('finishing-center', 'FinishingWorkController')->parameters(['finishing-center' => 'finish']);
    Route::post('/finishing-center/search', 'FinishingWorkController@search');
    Route::post('/finishing-center/search-non', 'FinishingWorkController@search_non');
    Route::get('finishing-center/finish-work/{carpet}', 'FinishingWorkController@saving_the_work');
    Route::get('finishing-center/re-finish-work/{carpet}', 'FinishingWorkController@re_saving_the_work');
    Route::post('/finishing-center/refinish', 'FinishingWorkController@store_refinish');
    Route::get('/finishing-center/search-finish-number/{finish_number},{team_id}', 'FinishingWorkController@search_finish_number');
    Route::post('/search-from-finish-number', 'FinishingWorkController@search_from_finish_number');
    Route::get('/return-to-wash/{id}','FinishingWorkController@return_to_wash');
    Route::get('/return-to-center-from-finish/{carpet_id}','FinishingWorkController@return_to_center');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:approve_refinish_requests']], function () {
    Route::get('refinish-request-list', 'FinishingWorkController@request_list');
    Route::delete('refinish-approve-request/{id}', 'FinishingWorkController@approve_request');
    Route::delete('refinish-delete-request/{id}', 'FinishingWorkController@delete_request');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:manage_finishing_payments']], function () {
    //  FINISHING RECEIVEDS
    Route::post('/finishing-payments/allocate', 'FinishingTeamPaymentController@allocateAdvance');
    Route::delete('/finishing-payments/allocation/{id}', 'FinishingTeamPaymentController@removeAllocation');
    Route::resource('finishing-payments', 'FinishingTeamPaymentController');
    Route::get('/finishing-payments-all/{team_id}', 'FinishingTeamPaymentController@show_all_payment');
});
/** End sales and finishing center routes */


/** Sales and Finishing Center Routes */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_sales']], function () {

/** Route For Sales */
Route::resource('/sales', 'SaleController');
Route::get('/sales-all', 'SaleController@show_all');
Route::get('/sales/export/pdf', 'SaleController@exportPdf')->name('sales.pdf_export');
Route::post('/search-carpet-from-sales','SaleController@search');
Route::get('/get_carpet_details.get_by_carpet', 'SaleController@get_carpet_details')->name('dashboard.get_carpet_details.get_by_carpet');



Route::resource('/packing-list', 'PakingListController');
Route::resource('/package-list', 'PackageController');
Route::get('/package_list.get_by_packing', 'PackageController@get_by_packing')->name('dashboard.package_list.get_by_packing');

});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_material_stock']], function () {
    /** Route for Material Accounts */
    Route::resource('/material-accounts', 'MaterialAccountController');
    Route::any('/material-accounts/search', 'MaterialAccountController@search');

    /** Route for Material Account Payments */
    Route::resource('/material-account-payments', 'MaterialAccountPaymentController');
    Route::delete('material-account-approve-request/{id}', 'MaterialAccountPaymentController@approve_request');
    Route::delete('material-account-delete-request/{id}', 'MaterialAccountPaymentController@delete_request');
    Route::get('material-account-request-list', 'MaterialAccountPaymentController@request_material');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_material_categories']], function () {
    /** Route For Material Category**/
    Route::resource('/material-category', 'MaterialCategoryController');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:manage_agent_accounts']], function () {
    Route::get('/agent-payment/{id}', 'AgentsController@agent_payment');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_provinces']], function () {
    /** Route For Province **/
    Route::resource('/provinces', 'ProvinceController');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_material_types']], function () {
    /** Route For Material Types **/
    Route::resource('/materialtypes', 'MaterialTypeController');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_agent_employees']], function () {
    /** Route For Material Types **/
    Route::resource('/agent-employees', 'AgentEmployeeController');
});


Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_string_sellers']], function () {
    // Route for string seller
    Route::resource('/string-seller', 'StringSellerController');
    Route::post('/string-seller/search', 'StringSellerController@search');
    Route::post('/string-seller-payments/allocate-advance', 'SellerPaymentController@allocateAdvance')->name('string-seller-payments.allocate_advance');
    Route::delete('/string-seller-payments/allocation/{id}', 'SellerPaymentController@removeAllocation');
    Route::resource('/string-seller-payments', 'SellerPaymentController');
    Route::get('/seller-payments-all/{seller_id}', 'SellerPaymentController@show_all_payment');
    Route::get('/sttring-seller-accounts', 'StringSellerController@accounts');
    Route::post('/string-seller/note/{id}', 'StringSellerController@updateNote')->name('string_seller.update_note');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_material_purchases']], function () {
    /** Route for Material purchase */
    Route::resource('/material-purchase', 'PurchaseMaterialController')->parameters(['material-purchase' => 'purchaseMaterial']);
    Route::get('/material-purchase/search-purchase-number/{purchase_number},{seller_id}', 'PurchaseMaterialController@search_purchase_number');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_raw_material_bills']], function () {
    /** Route for Raw Material Purchase Bills */
    Route::resource('/raw-material-purchase-bills', 'RawMaterialPurchaseBillController')->names([
        'index'   => 'raw-material-purchase-bills.index',
        'create'  => 'raw-material-purchase-bills.create',
        'store'   => 'raw-material-purchase-bills.store',
        'edit'    => 'raw-material-purchase-bills.edit',
        'update'  => 'raw-material-purchase-bills.update',
        'destroy' => 'raw-material-purchase-bills.destroy',
    ]);
    Route::get('/raw-material-purchase-bills/by-seller/{seller_id}', 'RawMaterialPurchaseBillController@getBySeller')->name('raw-material-purchase-bills.by-seller');
    Route::post('/raw-material-purchase-bills/{id}/close', 'RawMaterialPurchaseBillController@closeBill')->name('raw-material-purchase-bills.close');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_material_stock']], function () {
    /** Route for Material stock */
    Route::resource('/material-stock', 'MaterialStockController');
    Route::get('/material-stock/history/{cat}/{type}', 'MaterialStockController@history')->name('material-stock.history');
    Route::post('/material-stock/search', 'MaterialStockController@search');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_material_sales']], function () {
    /** Route for material sales */
    Route::get('/material-sales/search-sale-number/{sale_number},{agent_id}', 'MaterialSaleController@search_sale_number');
    Route::resource('/material-sales', 'MaterialSaleController');
    Route::get('/material-sales-info', 'MaterialSaleController@get_sale_info')->name('dashboard.material-sales-info');
    /** Route for  Received of Material */
    Route::resource('/carpet-material', 'CarpetMaterialController')->parameters(['carpet-material' => 'material']);
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_buy_carpets']], function () {
    /** Route For Contract Carpets  */
    Route::resource('/contract-carpet', 'CarpetsController')->parameters(['contract-carpet' => 'carpet']);
    Route::post('/contract-carpet/search', 'CarpetsController@search_contract_carpet');
    Route::get('contract-carpet-show-all', 'CarpetsController@show_all_contract_carpet');
    
    Route::post('/pass-parcha','CarpetsController@pass_parcha');
    Route::post('/search-contract-carpet-by-agent','CarpetsController@search_contract_carpet_by_agent');


    Route::get('/list-weight', 'CarpetsController@listWeight');
    Route::post('/list-weight/search', 'CarpetsController@search_weight_carpet');
    Route::post('/post-weight', 'CarpetsController@PostWeight');
    Route::get('/edit-weight/{id}', 'CarpetsController@editWeight');
    Route::post('/upd-weight/{id}', 'CarpetsController@UpdatetWeight');
    Route::get('/show-weight/{id}', 'CarpetsController@showWeight');
    Route::get('list-weight-show-all', 'CarpetsController@show_all_weight_carpet');
    Route::post('/search-weight-carpet-by-agent','CarpetsController@search_weight_carpet_by_agent');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_purchase_bills']], function () {
    // Route for seller check book
    Route::post('check-book/{id}/close', 'CarpetCheckBookController@closeInvoice')->name('check-book.close');
    Route::resource('/check-book', 'CarpetCheckBookController')->parameters(['check-book' => 'chck']);
    Route::post('check-book/search', 'CarpetCheckBookController@Filter');
    Route::post('check-book/search-agent', 'CarpetCheckBookController@search_agent');
    Route::post('check-book/search-check-number/', 'CarpetCheckBookController@search_check_number');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_carpet_repairs']], function () {
    // Route for carpet repair
    Route::get('/carpet-repair-create/{id}', 'CarpetRepairController@createRepair');
    Route::resource('/carpet-repair', 'CarpetRepairController');
    Route::get('/carpet-repair/search-kachaee-number/{kachaee_number}{team_id}', 'CarpetRepairController@search_kachaee_number');

    Route::post('/repair-search', 'CarpetRepairController@repair_search');
    Route::post('/search-repaired', 'CarpetRepairController@search_repaired');
    Route::post('/repair-date-search', 'CarpetRepairController@repair_date_search');

    Route::match(['get', 'post'], '/return-to-center-from-non-repair/{id}','CarpetRepairController@return_to_center_from_non_repair');
    Route::match(['get', 'post'], '/return-to-center-from-repair/{id}','CarpetRepairController@return_to_center_from_repair');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_agent_carpets']], function () {
    // Agent Carpets Details
    Route::post('/agent-carpet/search', 'AgentsCarpetController@agent_carpet_search');
    Route::get('agent-balance/{carpet}', 'AgentsCarpetController@agent_balance');
});


Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_kachaee_teams']], function () {
    // Route for Kachaee team
    Route::resource('kachaee-team', 'KachaeeController')->parameters(['kachaee-team' => 'team']);
    Route::post('/kachaee-payments/allocate', 'KachaeePaymentController@allocateAdvance');
    Route::delete('/kachaee-payments/allocation/{id}', 'KachaeePaymentController@removeAllocation');
    Route::resource('kachaee-payments', 'KachaeePaymentController');
    Route::get('/kachaee-payments-all/{team_id}', 'KachaeePaymentController@show_all_payment');
    Route::get('/kachaee-accounts', 'KachaeeController@accounts');
    Route::post('/kachaee-team/search', 'KachaeeController@search');
    Route::post('/kachaee-team/note/{id}', 'KachaeeController@updateNote')->name('kachaee_team.update_note');

    // These used to be here, but moved to their specific sending route groups
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_buy_carpets']], function () {
    Route::get('carpets-in-center-office', 'CarpetsController@carpets_in_center_office');

    /** route for buy carpet */
    Route::get('/create-buy-carpet', 'CarpetsController@createBuyCarpet');
    // Purchased Carpets Report
    Route::get('/purchased-carpets-report', 'PurchasedCarpetReportController@index')->name('purchased.carpets.index');
    Route::get('/purchased-carpets-report/excel', 'PurchasedCarpetReportController@exportExcel')->name('purchased.carpets.excel');
    Route::get('/purchased-carpets-report/pdf', 'PurchasedCarpetReportController@exportPdf')->name('purchased.carpets.pdf');

    Route::get('/list-buy-carpet', 'CarpetsController@listBuyCarpet');
    Route::get('/list-buy-carpet/show-all', 'CarpetsController@show_all_buy_carpet');
    Route::post('/list-buy-carpet/search', 'CarpetsController@search_buy_carpet');
    Route::post('/search-buy-carpet-by-agent','CarpetsController@search_buy_carpet_by_agent');
    Route::post('/post-buy-carpet', 'CarpetsController@PostBuyCarpet');
    Route::get('/edit-buy-carpet/{id}', 'CarpetsController@editBuyCarpet');
    Route::post('/upd-buy-carpet/{id}', 'CarpetsController@UpdatetBuyCarpet');
    Route::get('/show-buy-carpet/{id}', 'CarpetsController@showBuyCarpet');
    Route::get('/print-buy-carpet/{id}', 'CarpetsController@printBuyCarpet');

    Route::get('/carpet-stock/sent-to-stock/{id}', 'CarpetsController@sending_to_stock');
});
/** End Central office routes */

/** SENDING ROUTES */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:send_carpet_to_washing']], function () {
    Route::get('washing-team/sending-to-washing/{carpetId}', 'WashingTeamController@sending_to_washing');
    Route::post('washing-team/sent-to-washing/{carpetId}', 'WashingTeamController@washing_team_selected');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:send_carpet_to_kachaee']], function () {
    Route::post('/carpet-wash/sent-to-kachaee/{carpet}', 'CarpetWashController@sent_to_kachaee');
    Route::get('carpet-repaire/sending-to-kachaee/{carpetId}', 'CarpetRepairController@sending_to_repair');
    Route::post('carpet-repair/sent-to-repair/{carpetId}', 'CarpetRepairController@repair_team_selected');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:send_carpet_to_finishing']], function () {
    Route::match(['get', 'post'], '/carpet-wash/sent-to-finish/{carpet}', 'CarpetWashController@sent_to_finishing_center');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_agents']], function () {
    /** Route For Agents**/
    Route::any('/agents/search', 'AgentsController@search');
    Route::resource('/agents', 'AgentsController');
    Route::post('/agent/phone/{id}', 'AgentsController@StorePhone');
    Route::get('/agent/phone/{id}', 'AgentsController@DeletePhone');
    Route::get('/agent-accounts', 'AgentsController@accounts');
    Route::get('/agent-deactive', 'AgentsController@deactive_accounts');
    Route::get('/agent-status-change/{agent_id}', 'AgentsController@change_status');
    Route::post('/agent/note/{id}', 'AgentsController@updateNote')->name('agents.update_note');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_phone_book']], function () {
    /** Route For Phone Book **/
    Route::resource('/phone-book', 'PhoneBookController');
    Route::post('/phone-books/search','PhoneBookController@search');
});


Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_carpet_types']], function () {
    /** Route For Carpet Type **/
    Route::resource('/carpet-types', 'CarpetTypeController');

    /** Route For Quality **/
    Route::resource('/carpet-qualities', 'QualityController');
    Route::get('/qualities.get_by_type', 'QualityController@get_by_type')->name('dashboard.qualities.get_by_type');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_orders']], function () {
    /** Route for seller purches */
    Route::resource('/carpet-orders', 'CarpetOrderController');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_agent_carpets']], function () {
    Route::resource('/agent-carpet', 'AgentsCarpetController');
    Route::get('/agent-carpets/{id}', 'AgentsCarpetController@carpet_details');
});


    // RECEIVED OF WASHING TEAM (Controller missing - disabled)
    // Route::resource('received-of-washing', 'ReceivedOfWashingController')->parameters(['received-of-washing' => 'r_washing']);


Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_washing_teams']], function () {
    // WASHING TEAM ROUTES
    Route::resource('washing-team', 'WashingTeamController')->parameters(['washing-team' => 'team']);
    Route::post('/washing-payments/allocate', 'WashingPaymentController@allocateAdvance');
    Route::delete('/washing-payments/allocation/{id}', 'WashingPaymentController@removeAllocation');
    Route::resource('washing-payments', 'WashingPaymentController');
    Route::get('/washing-payments-all/{team_id}', 'WashingPaymentController@show_all_payment');
    Route::get('/washing-accounts', 'WashingTeamController@accounts');
    Route::post('/washing-team/search', 'WashingTeamController@search');
    Route::get('washing-team-carpets/{id}', 'WashingTeamController@team_carpets');
    Route::post('/washing-team/note/{id}', 'WashingTeamController@updateNote')->name('washing_team.update_note');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_invoices']], function () {
    /** Route For invoices */
    Route::resource('/invoices', 'InvoiceController');
    Route::post('/invoices/{id}/close', 'InvoiceController@closeInvoice');
    Route::post('/search-invoice','InvoiceController@search');
    Route::get('/invoices/search-invoice-number/{invoice_number},{customer_id}', 'InvoiceController@search_invoice_number');
    Route::post('/search-carpet-from-invoice', 'InvoiceController@search_carpet');
    Route::get('/invoices/sent-to-stock/{id}', 'InvoiceController@sending_to_stock');
});


Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_carpet_washes']], function () {
    // WASHING CARPET ROUTES
    Route::resource('carpet-wash', 'CarpetWashController')->parameters(['carpet-wash' => 'wash']);
    Route::get('carpet-wash/create/{carpetId}', 'CarpetWashController@create_carpet_wash');
    Route::post('/carpet-wash/search', 'CarpetWashController@search');
    Route::post('/carpet-wash/search-carpet-type', 'CarpetWashController@search_carpet_type');
    Route::get('/carpet-wash/wash-numbers/{id}', 'CarpetWashController@wash_numbers');
    Route::post('/search-wash-number-for-wash/', 'CarpetWashController@search_wash_number_for_wash');
    Route::post('/search-carpet-type-from-wash-number/','CarpetWashController@search_carpet_type_from_wash_number');
    Route::post('/search-wash-number-sh-for-wash/', 'CarpetWashController@search_wash_numbersh_for_wash');
    Route::get('/search-wash-numbersh-payment/{wash_number_sh},{team_id}', 'CarpetWashController@search_wash_numbersh_payment');
    Route::get('/carpet-wash/return-to-center/{id}','CarpetWashController@return_to_center');
    Route::match(['get', 'post'], '/carpet-wash/return-to-kachaee/{id}','CarpetWashController@return_to_kachaee');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_carpet_stock']], function () {
    Route::get('/all-carpet-edit/{id}', 'DashboardController@all_carpet_edit_dashboard');
});
/**  End shared routes */

/** route for sales center and admin */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_employees']], function () {
    /** Route For Office Employee Departments */
    Route::resource('/employee-department', 'EmployeeDepartmentController');

    /** Route For Office Employee */
    Route::resource('/office-employee', 'OfficeEmployeeController');
    /** Route For Office Employee Search */
    Route::post('/office-employee/search', 'OfficeEmployeeController@search');

    /** Route For Office Employee Salary */
    Route::resource('/employee-salary', 'EmployeeSalaryController');
    /** Route For Employee Payment (Cash Ledger: Loans / Advances) */
    Route::resource('employee-payments', 'EmployeePaymentController');
    Route::get('/employee-payments-all/{employee_id}', 'EmployeePaymentController@show_all_payment');
    Route::post('/employee-payments-list-contract', 'EmployeePaymentController@show_contract_payment');

    /** Route For Monthly Payroll (Salary Run) */
    Route::resource('/payroll', 'PayrollController')->names('payroll');
    Route::get('/payroll/{run}/slip/{item}', 'PayrollController@slip')->name('payroll.slip');
});


Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_monthly_expenses']], function () {
    /** Route For Employee Payment */
    Route::resource('/expenses', 'OfficeDebitController');
    Route::post('/expenses/search', 'OfficeDebitController@search');
    /** Route For Cash Book List */
    Route::resource('/office-cash-book', 'OfficeCashBookController');
    
    Route::resource('/monthly-expenses','MonthlyExpenseController');
    Route::any('/monthly-expenses-search','MonthlyExpenseController@search');

     Route::resource('/monthly-expense-accounts','NewMonthlyExpenseController');

    Route::resource('/new-monthly-expense-payments','NewMonthlyExpenseBalanceController');
});
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_customer_orders']], function () {
    Route::resource('/customer-account-for-orders','CustomerAccountOrderController');
    Route::get('/customer-orders-next-number', 'CustomerOrderController@getNextOrderNumber');
    Route::get('/customer-orders/{customer_id}/export/pdf', 'CustomerOrderController@exportPdf')->name('customer_orders.export_pdf');
    Route::get('/customer-orders/{customer_id}/export/excel', 'CustomerOrderController@exportExcel')->name('customer_orders.export_excel');
    Route::resource('/customer-orders','CustomerOrderController');
    Route::resource('/customer-order-details','CustomerOrderDetailsController');
    Route::get('/carpet-specification/{id}', 'CustomerOrderDetailsController@show_carpet');
    Route::post('/customer-order-details/{id}/receive', 'CustomerOrderDetailsController@receiveIntoStock');
    Route::post('/customer-order-details/{id}/sell', 'CustomerOrderDetailsController@processFinalSale');
    Route::patch('/customer-order-details/{id}/change-status', 'CustomerOrderDetailsController@changeStatus');

    Route::get('/close-to-end-customer-order','CustomerOrderDetailsController@close_to_end_customer_order');
});
    
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_assets_accounts']], function () {
     //rout for assets account list
    Route::resource('/assets-accounts','AjnasAccountController');
    Route::resource('/assets-accounts-details','AjnasAccountDetailsController');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_assets_report']], function () {
    // Assets Accounts Report
    Route::get('/assets-report', 'AssetsReportController@index')->name('assets.report');
    Route::get('/assets-report/excel', 'AssetsReportController@exportExcel')->name('assets.report.excel');
    Route::get('/assets-report/pdf', 'AssetsReportController@exportPdf')->name('assets.report.pdf');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_monthly_expenses']], function () {
    /** Route For all expense */
    Route::get('all-expenses', 'OfficeCashBookController@all_expenses');

    /** Route for expense details */
    Route::resource('expense-details', 'ExpenseDetailsController');

    /** Route For expense Search date range */
    Route::post('/office-cash-book/search-date-range', 'OfficeCashBookController@expense_search_date_range');

    /** Route For expense Search */
    Route::post('/office-cash-book/search', 'OfficeCashBookController@expense_search');

    /** Route For add New Expense */
    Route::get('/add-new-expense', 'OfficeDebitController@add_new_expense');

    /** Route For Add Credit */
    Route::resource('/add-office-credit', 'OfficeCreditController');
});


/** route for admin and center and sale with customer */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_customers']], function () {
    /** Route for carpet customers */
    Route::resource('/customers', 'CustomerController');

    /** Route For customer payments */
    Route::get('/get-outstanding-invoices', 'CustomerPaymentController@get_outstanding_invoices')->name('dashboard.customer_payments.get_outstanding');
    Route::resource('customer-payments', 'CustomerPaymentController');
    Route::get('customer-payments-all/{customer_id}', 'CustomerPaymentController@show_all_payment');

    Route::get('/customer-accounts', 'CustomerController@accounts');
    Route::post('/customers/search', 'CustomerController@search');
    Route::post('/customers/note/{id}', 'CustomerController@updateNote')->name('customers.update_note');
});
    
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_trial_balance']], function () {
    /** Accounting Reports */
    Route::group(['prefix' => 'reports/accounting'], function () {
        Route::get('/dashboard', 'AccountingReportController@dashboard')->name('accounting.dashboard');
        Route::get('/trial-balance', 'AccountingReportController@trialBalance')->name('accounting.reports.trial-balance');
        Route::get('/income-statement', 'AccountingReportController@incomeStatement')->name('accounting.reports.income-statement');
        Route::post('/close-period', 'AccountingReportController@closePeriod')->name('accounting.close_period');
    });
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_ar_aging_report']], function () {
    /** AR Aging Report */
    Route::group(['prefix' => 'reports/accounting'], function () {
        Route::get('/ar-aging', 'AccountingReportController@arAging')->name('accounting.reports.ar-aging');
    });
});

/** Accounting Dashboard */
Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:view_finance_dashboard']], function () {
    /** Dashboard */
    Route::get('/', 'Accounting\DashboardController@index')->name('accounting.dashboard');
});

/** Accounting Modules */
Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:view_coa']], function () {
    
    /** Chart of Accounts */
    Route::get('/chart-of-accounts', 'Accounting\ChartOfAccountController@index')->name('accounting.coa.index');
    Route::get('/chart-of-accounts/create', 'Accounting\ChartOfAccountController@create')->name('accounting.coa.create');
    Route::post('/chart-of-accounts', 'Accounting\ChartOfAccountController@store')->name('accounting.coa.store');
    Route::get('/chart-of-accounts/{id}/edit', 'Accounting\ChartOfAccountController@edit')->name('accounting.coa.edit');
    Route::put('/chart-of-accounts/{id}', 'Accounting\ChartOfAccountController@update')->name('accounting.coa.update');

    /** Journal Vouchers */
    Route::get('/journals', 'Accounting\JournalController@index')->name('accounting.journals.index');
    Route::get('/journals/create', 'Accounting\JournalController@create')->name('accounting.journals.create');
    Route::get('/journals/api/parties', 'Accounting\JournalController@getParties')->name('accounting.journals.api.parties');
    Route::post('/journals', 'Accounting\JournalController@store')->name('accounting.journals.store');
    Route::post('/journals/export', 'Accounting\JournalController@exportReport')->name('accounting.journals.export');
    Route::get('/journals/{id}', 'Accounting\JournalController@show')->name('accounting.journals.show');
    Route::get('/journals/{id}/print', 'Accounting\JournalController@print')->name('accounting.journals.print');
    Route::post('/journals/{id}/reverse', 'Accounting\JournalController@reverse')->name('accounting.journals.reverse');

    /** Mapping Rules */
    Route::get('/mapping-rules', 'Accounting\MappingRuleController@index')->name('accounting.mappings.index');
    Route::post('/mapping-rules', 'Accounting\MappingRuleController@update')->name('accounting.mappings.update');

    // Warehouse Management routes moved to separate permission groups below

    /** Financial Reports */
    Route::get('/reports/trial-balance', 'Accounting\ReportController@trialBalance')
        ->middleware('permission:view_trial_balance')->name('accounting.reports.trial_balance');
    Route::get('/reports/profit-loss', 'Accounting\ReportController@profitLoss')
        ->middleware('permission:view_pl_report')->name('accounting.reports.profit_loss');
    Route::get('/reports/balance-sheet', 'Accounting\ReportController@balanceSheet')
        ->middleware('permission:view_balance_sheet')->name('accounting.reports.balance_sheet');
    Route::get('/reports/comparative-pl', 'Accounting\ReportController@comparativePL')
        ->middleware('permission:view_comparative_pl')->name('accounting.reports.comparative_pl');
    Route::get('/reports/inventory-valuation', 'Accounting\ReportController@inventoryValuation')
        ->middleware('permission:view_inventory_valuation')->name('accounting.reports.inventory_valuation');
    Route::get('/reports/fx-exposure', 'Accounting\ReportController@fxExposure')
        ->middleware('permission:view_fx_exposure_report')->name('accounting.reports.fx_exposure');
    Route::get('/reports/cost-centers', 'Accounting\ReportController@costCenterPerformance')
        ->middleware('permission:view_cost_center_performance')->name('accounting.reports.cost_center_performance');
    Route::get('/reports/audit-corrections', 'Accounting\ReportController@auditCorrections')
        ->middleware('permission:view_audit_corrections')->name('accounting.reports.audit_corrections');
    Route::get('/reports/cash-flow', 'Accounting\ReportController@cashFlow')
        ->middleware('permission:view_cash_flow_report')->name('accounting.reports.cash_flow');
    Route::get('/reports/account-ledger', 'Accounting\ReportController@accountLedger')
        ->middleware('permission:view_account_ledger')->name('accounting.reports.account_ledger');


    Route::get('/api/allowed-accounts', 'Accounting\AccountController@getAllowedAccounts')->name('accounting.api.allowed_accounts');
});

/** Warehouse Management Routes */
Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:view_warehouses']], function () {
    Route::get('/warehouses', 'Accounting\WarehouseController@index')->name('accounting.warehouses.index');
});

Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:create_warehouse']], function () {
    Route::post('/warehouses', 'Accounting\WarehouseController@store')->name('accounting.warehouses.store');
});

Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:edit_warehouse']], function () {
    Route::put('/warehouses/{id}', 'Accounting\WarehouseController@update')->name('accounting.warehouses.update');
});

Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:delete_warehouse']], function () {
    Route::delete('/warehouses/{id}', 'Accounting\WarehouseController@destroy')->name('accounting.warehouses.destroy');
});

// Stock report accessible by sidebar-level OR per-warehouse-kabab permission
Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:view_warehouse_inventory_report|view_warehouse_available_stock']], function () {
    Route::get('/warehouses/{id}/stock-report', 'Accounting\WarehouseController@stockReport')->name('accounting.warehouses.stock_report');
    Route::get('/warehouses/{id}/stock-report/pdf', 'Accounting\WarehouseController@stockReportPdf')->name('accounting.warehouses.stock_report_pdf');
    Route::get('/warehouses/{id}/stock-report/excel', 'Accounting\WarehouseController@stockReportExcel')->name('accounting.warehouses.stock_report_excel');
});

/** Warehouse Transfers Routes */

// Static routes MUST come before parameterized {id} routes to avoid shadowing
Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:create_inventory_transfer']], function () {
    Route::get('/transfers/create', 'Accounting\InventoryTransferController@create')->name('accounting.transfers.create');
    Route::post('/transfers', 'Accounting\InventoryTransferController@store')->name('accounting.transfers.store');
});

Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:view_inventory_transfers']], function () {
    Route::get('/transfers', 'Accounting\InventoryTransferController@index')->name('accounting.transfers.index');
    Route::get('/transfers/api/items', 'Accounting\InventoryTransferController@getWarehouseItems')->name('accounting.transfers.api.items');
    Route::get('/transfers/{id}', 'Accounting\InventoryTransferController@show')->name('accounting.transfers.show');
});

Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:reverse_inventory_transfer']], function () {
    Route::post('/transfers/{id}/reverse', 'Accounting\InventoryTransferController@reverse')->name('accounting.transfers.reverse');
});

/** Warehouse Movements Routes */
// Movements page accessible by sidebar-level OR per-warehouse-kabab permission
Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:view_warehouse_movements|view_warehouse_in_out_report']], function () {
    Route::get('/warehouse-movements', 'Accounting\WarehouseMovementController@index')->name('accounting.warehouses.movements');
});

Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:view_customer_statement']], function () {
    Route::get('/reports/customer-statement', 'Accounting\ReportController@customerStatement')->name('accounting.reports.customer_statement');
});

Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'permission:view_agent_statement']], function () {
    Route::get('/reports/agent-statement', 'Accounting\ReportController@agentStatement')->name('accounting.reports.agent_statement');
});
/**  */

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_different_accounts']], function () {
//    Route::get('/', 'DashboardController@Index');
    /** Route for different account */
    Route::resource('/different-account', 'DifferentAccountController');
    Route::get('different-account-payments-all/{account_id}', 'DifferentAccountController@show_all_payment');
    Route::post('different-account/search', 'DifferentAccountController@search');
    Route::post('different-account/note/{id}', 'DifferentAccountController@updateNote')->name('different_account.update_note');

    /** Route for different account received */
    // Route::resource('/different-account-receiveds', 'DifferentAccountReceviedController')->parameters(['different-account-receiveds' => 'differentAccountRecevied']); // Controller missing

    /** Route for different account payments */
    Route::resource('/different-account-payments', 'DifferentAccountPaymentController')->parameters(['different-account-payments' => 'differentAccountPayment']);
    
    
    /** Route for new different account */
    Route::resource('/new-different-account', 'NewDifferentAccountController');

    Route::get('new-different-account-payments-all/{account_id}', 'NewDifferentAccountController@show_all_payment');
    Route::post('new-different-account/search', 'NewDifferentAccountController@search');
    /** Route for different account payments */
    Route::resource('/new-different-account-payments', 'NewDifferentAccountPaymentController')->parameters(['new-different-account-payments' => 'NewdifferentAccountPayment']);



});
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function () {
    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::get('/', 'EnterpriseDashboardController@index')->middleware('permission:view_production_dashboard');
    Route::get('/production', 'EnterpriseDashboardController@production')->middleware('permission:view_production_dashboard');
    Route::get('/inventory', 'EnterpriseDashboardController@inventory')->middleware('permission:view_inventory_dashboard');
    Route::get('/sales-dashboard', 'EnterpriseDashboardController@sales')->middleware('permission:view_sales_dashboard');
    Route::get('/purchases', 'EnterpriseDashboardController@purchases')->middleware('permission:view_purchases_dashboard');
    Route::get('/finance', 'EnterpriseDashboardController@finance')->middleware('permission:view_finance_dashboard');
    Route::get('/cost-analytics', 'EnterpriseDashboardController@costAnalytics')->middleware('permission:view_cost_analytics');
    
    Route::get('/all-carpet-dashboard-show-all', 'DashboardController@show_all')->middleware('permission:view_production_dashboard');
    Route::post('/all-carpet-dashboard/search', 'DashboardController@search_all_carpet_dashboard')->middleware('permission:view_production_dashboard');
    Route::any('/search-this-month-carpet','DashboardController@search_this_month_carpet')->middleware('permission:view_production_dashboard');
    Route::get('/all-exesting-carpet', 'DashboardController@show_existing_carpet')->middleware('permission:view_production_dashboard');
});
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:view_agent_statement|view_monthly_expenses|view_customer_statement|view_production_dashboard']], function () {
    ///  REPORTS //
    Route::get('/agent_balance_report','ReportController@agent_balance_report');
    Route::any('/get_agent_balance_report','ReportController@get_agent_balance_report');
    
    Route::get('/different_account_balance_report','ReportController@different_account_balance_report');
    Route::any('/get_different_account_balance_report','ReportController@get_different_account_balance_report');
    
    Route::get('/kachaee_team_balance_report','ReportController@kachaee_team_balance_report');
    Route::any('/get_kachaee_team_balance_report','ReportController@get_kachaee_team_balance_report');
    
    Route::get('/washing_team_balance_report','ReportController@washing_team_balance_report');
    Route::any('/get_washing_team_balance_report','ReportController@get_washing_team_balance_report');
    
    Route::get('/finishing_team_balance_report','ReportController@finishing_team_balance_report');
    Route::any('/get_finishing_team_balance_report','ReportController@get_finishing_team_balance_report');
    
    Route::get('/string_seller_balance_report','ReportController@string_seller_balance_report');
    Route::any('/get_string_seller_balance_report','ReportController@get_string_seller_balance_report');
    
    Route::get('/customer_balance_report','ReportController@customer_balance_report');
    Route::any('/get_customer_balance_report','ReportController@get_customer_balance_report');
    
    Route::get('/purchase_carpet_report','ReportController@purchase_carpet_report');
    Route::any('/get_purchase_carpet_report','ReportController@get_purchase_carpet_report');
    Route::get('/sales_report','ReportController@sales_report');
    Route::any('/get_sales_report','ReportController@get_sales_report');

    /** ERP Inventory Reports (Phase 4) */
    Route::get('/erp-inventory-report', 'InventoryReportController@index')->name('inventory.reports.index');
    Route::get('/erp-inventory-report/{id}', 'InventoryReportController@detail')->name('inventory.reports.detail');
    Route::get('/erp-wip-report', 'InventoryReportController@wipReport')->name('inventory.reports.wip');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission:manage_agent_accounts']], function () {
    /** Route for Agent Payment */
    Route::post('agent-payments/allocate', 'AgentPaymentController@allocateAdvance');
    Route::delete('agent-payments/allocation/{id}', 'AgentPaymentController@removeAllocation');
    Route::resource('/agent-payments', 'AgentPaymentController')->parameters(['agent-payments' => 'agent']);
    Route::get('agent-payments-all/{agent_id}', 'AgentPaymentController@show_all');
    Route::get('check-book/search-check-number-payment/{check_number},{agent_id}', 'CarpetCheckBookController@search_check_number_payment');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function () {
    Route::get('/statements/{entity}/{id}', 'Accounting\EntityStatementController@show')->name('accounting.statements.show');
    Route::get('/reports/statements/{entity}', 'Accounting\EntityStatementController@reportStatement')->name('accounting.reports.entity_statement');
    Route::get('/accounting/reports/different-account-statement', 'Accounting\ReportController@differentAccountStatement')
        ->middleware('permission:view_different_account_statement')
        ->name('accounting.reports.different_account_statement');
    Route::get('/accounting/reports/repair-team-statement', 'Accounting\ReportController@repairTeamStatement')
        ->middleware('permission:view_kachaee_team_statement')
        ->name('accounting.reports.repair_team_statement');
    Route::get('/accounting/reports/washing-team-statement', 'Accounting\ReportController@washingTeamStatement')
        ->middleware('permission:view_washing_team_statement')
        ->name('accounting.reports.washing_team_statement');
    Route::get('/accounting/reports/finishing-team-statement', 'Accounting\ReportController@finishingTeamStatement')
        ->middleware('permission:view_finishing_team_statement')
        ->name('accounting.reports.finishing_team_statement');
    Route::get('/accounting/reports/string-seller-statement', 'Accounting\ReportController@stringSellerStatement')
        ->middleware('permission:view_seller_statement')
        ->name('accounting.reports.string_seller_statement');
    Route::get('/accounting/reports/employee-statement', 'Accounting\ReportController@employeeStatement')
        ->middleware('permission:view_employee_statement')
        ->name('accounting.reports.employee_statement');
    Route::get('/batches/{type}', 'ProductionBatchController@index')->name('batches.index');
    Route::get('/batches/api/{type}/open', 'ProductionBatchController@getOpenBatches')->name('batches.open');
    Route::post('/batches/{type}', 'ProductionBatchController@store')->name('batches.store');
    Route::post('/batches/{id}/toggle-status', 'ProductionBatchController@toggleStatus')->name('batches.toggle-status');
    Route::get('/batches/{id}/details', 'ProductionBatchController@details')->name('batches.details');
    
    // Global Search Routes
    Route::get('/global-search/carpets', 'GlobalSearchController@searchCarpets')->name('global-search.carpets');
    Route::get('/global-search/carpet/{id}', 'GlobalSearchController@showCarpet')->name('global-search.carpet.details');
});

Auth::routes();
Route::redirect('/', '/login');

Route::fallback(function () {
    abort(404);
});


Route::get('/logout', function () {
    
    if (Auth::check()) {
        $activity = new Activity();
        $activity->date = \Carbon\Carbon::today()->format('Y-m-d');
        $activity->description =  "استفاده کننده بنام ". Auth::user()->name ." از سیستم خارج شد ";
        $activity->user_id = Auth::user()->id;
        $activity->save();
    }

    
    auth()->logout();
    Session()->flush();
    return Redirect::to('/');
})->name('logout');

Route::get('/test-missing-column', function () {
    return \Illuminate\Support\Facades\DB::select("select non_existent_column from carpets");
});
