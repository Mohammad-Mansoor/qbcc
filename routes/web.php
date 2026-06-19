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
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:SP']], function () {
 
    Route::get('activities', 'ActivityController@index');
    Route::delete('activities/{a_id}', 'ActivityController@destroy');
    Route::post('search_activities', 'ActivityController@search_activities');
    
    /** Route for different account */
    /** Route For Users **/
    Route::resource('/users', 'UserController');
    Route::post('/user-search', 'UserController@search');
    // Route::get('/edit-currency/{id}', 'DashboardController@editCurrency');
    // Route::put('/update-currency/{id}', 'DashboardController@updateCurrency');

    Route::resource('/currencies', 'Accounting\CurrencyController')->names([
        'index' => 'accounting.currencies.index',
        'create' => 'accounting.currencies.create',
        'store' => 'accounting.currencies.store',
        'edit' => 'accounting.currencies.edit',
        'update' => 'accounting.currencies.update',
        'destroy' => 'accounting.currencies.destroy',
    ]);


    /** route for all carpets list */
    Route::get('/all-carpets', 'CarpetsController@all_carpets');
    Route::post('/view-carpets/search', 'CarpetsController@all_carpets_search');
    Route::get('/view-carpets', 'CarpetsController@all_carpets_view');

    /** route for filter based on carpet type */

    Route::post('filter-based-carpet-type', 'CarpetsController@filter_based_carpet_type');

    /** talab mardom total */

    Route::get('talab-mardom/{type?}', 'DashboardController@talab_mardom');
    Route::get('qarz-mardom/{type?}', 'DashboardController@qarz_mardom');
    Route::get('money-request-list', 'OfficeCreditController@money_request');
    Route::delete('approve-request-money/{id}', 'OfficeCreditController@approve_request');
      Route::delete('delete-request-money/{id}', 'OfficeCreditController@delete_request');

    /** agent money request list  */

    Route::get('agent-money-request-list', 'AgentPaymentController@money_request');
    Route::delete('agent-approve-request-money/{id}', 'AgentPaymentController@approve_request');
    Route::delete('agent-delete-request-money/{id}', 'AgentPaymentController@delete_request');

    /** end agent money request list */
    
    
    
       /** different account money request list  */

    Route::get('different-account-money-request-list', 'DifferentAccountPaymentController@money_request');
    Route::delete('different-account-approve-request-money/{id}', 'DifferentAccountPaymentController@approve_request');
    Route::delete('different-account-delete-request-money/{id}', 'DifferentAccountPaymentController@delete_request');

    /** end different account money request list */
    
     /** new different account money request list  */

    Route::get('new-different-account-money-request-list', 'NewDifferentAccountPaymentController@money_request');
    Route::delete('new-different-account-approve-request-money/{id}', 'NewDifferentAccountPaymentController@approve_request');
    Route::delete('new-different-account-delete-request-money/{id}', 'NewDifferentAccountPaymentController@delete_request');

    /** end  new different account money request list */
    
    
      /** kachaee money request list  */

    Route::get('kachaee-money-request-list', 'KachaeePaymentController@money_request');
    Route::delete('kachaee-approve-request-money/{id}', 'KachaeePaymentController@approve_request');
    Route::delete('kachaee-delete-request-money/{id}', 'KachaeePaymentController@delete_request');

    /** end kachaee money request list */
    
    
        /** washing money request list  */

    Route::get('washing-money-request-list', 'WashingPaymentController@money_request');
    Route::delete('washing-approve-request-money/{id}', 'WashingPaymentController@approve_request');
    Route::delete('washing-delete-request-money/{id}', 'WashingPaymentController@delete_request');

    /** end washing money request list */
    

     /** finishing money request list  */

    Route::get('finishing-money-request-list', 'FinishingTeamPaymentController@money_request');
    Route::delete('finishing-approve-request-money/{id}', 'FinishingTeamPaymentController@approve_request');
    Route::delete('finishing-delete-request-money/{id}', 'FinishingTeamPaymentController@delete_request');

    /** end finishing money request list */
    
    
    
    
    /** purchase material  request list  */

    Route::get('purchase-material-request-list', 'PurchaseMaterialController@request_list');
    Route::delete('purchase-material-approve-request/{id}', 'PurchaseMaterialController@approve_request');
    Route::delete('purchase-material-delete-request/{id}', 'PurchaseMaterialController@delete_request');

    /** end purchase material request list */
    
    
    
    /**  material sale  request list  */
    Route::get('material-sales/request-list', 'MaterialSaleController@request_list');
    Route::get('material-sale-request-list', 'MaterialSaleController@request_list');   // alias (hyphenated form)
    Route::delete('material-sales/approve-request/{id}', 'MaterialSaleController@approve_request');
    Route::delete('material-sale-approve-request/{id}', 'MaterialSaleController@approve_request');  // alias
    Route::delete('material-sales/delete-request/{id}', 'MaterialSaleController@delete_request');
    Route::delete('material-sale-delete-request/{id}', 'MaterialSaleController@delete_request');    // alias
    /** end  material sale request list */
    
    
      /**  string seller request list  */

    Route::get('string-seller-request-list', 'SellerPaymentController@request_list');
    Route::delete('string-seller-approve-request/{id}', 'SellerPaymentController@approve_request');
    Route::delete('string-seller-delete-request/{id}', 'SellerPaymentController@delete_request');

    /** end string seller request list */
    
    
        /**  employee request list  */

    Route::get('employee-request-list', 'EmployeePaymentController@request_list');
    Route::delete('employee-approve-request/{id}', 'EmployeePaymentController@approve_request');
    Route::delete('employee-delete-request/{id}', 'EmployeePaymentController@delete_request');

    /** end employee request list */


   /** customer request list  */

    Route::get('customer-request-list', 'CustomerPaymentController@request_list');
    Route::delete('customer-approve-request/{id}', 'CustomerPaymentController@approve_request');
    Route::delete('customer-delete-request/{id}', 'CustomerPaymentController@delete_request');

    /** end customer request list */



    ///  REPORTS //
    Route::get('/agent_balance_report','ReportController@agent_balance_report');
    Route::any('/get_agent_balance_report','ReportController@get_agent_balance_report');

    Route::get('/expense_report','ReportController@expense_report');
    Route::any('/get_expense_report','ReportController@get_expense_report');
   


});

/** End super admin route */

/** Sales and Finishing Center Routes */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:SO,SCO,SP,DE,FI,CO,CCO,OM']], function () {


    /** Route For Carpet Stock  */
    Route::get('/carpet-stock', 'CarpetsController@carpet_stock');

    /** route for carpet stock filter based on type */
    Route::match(['get', 'post'], '/filter-ba-asas-type', 'CarpetsController@filter_ba_asas_type');

    /** Route For Carpet Search date range */
    Route::match(['get', 'post'], '/carpet-stock/search-date-range', 'CarpetsController@stock_search_date_range');
    /** Route For Carpet Stock Search */
    Route::match(['get', 'post'], '/carpet-stock/search', 'CarpetsController@carpet_stock_search');

    /** Route For Carpet Stock Details  */
    Route::get('/carpet-stock-details/{carpet_id}', 'CarpetsController@carpet_stock_details');



    // Route for finishing team Category
//    Route::resource('/finish-team-category', 'FinishingTeamCategoryController')->parameters(['finish-team-category' => 'category']);
    // Route for finishing team
    Route::resource('/finish-team', 'FinishingTeamController')->parameters(['finish-team' => 'team']);
    Route::post('/finish-team/search', 'FinishingTeamController@search');

    // FINISHING CENTER
    Route::resource('finishing-center', 'FinishingWorkController')->parameters(['finishing-center' => 'finish']);
    Route::post('/finishing-center/search', 'FinishingWorkController@search');
    Route::post('/finishing-center/search-non', 'FinishingWorkController@search_non');
    Route::get('finishing-center/finish-work/{carpet}', 'FinishingWorkController@saving_the_work');
     Route::get('finishing-center/re-finish-work/{carpet}', 'FinishingWorkController@re_saving_the_work');
    Route::post('/finishing-center/refinish', 'FinishingWorkController@store_refinish');
    Route::get('refinish-request-list', 'FinishingWorkController@request_list');
    Route::delete('refinish-approve-request/{id}', 'FinishingWorkController@approve_request');
    Route::delete('refinish-delete-request/{id}', 'FinishingWorkController@delete_request');
    //  FINISHING RECEIVEDS
    Route::resource('finishing-payments', 'FinishingTeamPaymentController');
    Route::get('/finishing-payments-all/{team_id}', 'FinishingTeamPaymentController@show_all_payment');
    Route::get('/finishing-accounts', 'FinishingTeamController@accounts');
    Route::get('/finishing-center/search-finish-number/{finish_number},{team_id}', 'FinishingWorkController@search_finish_number');
    Route::post('/search-from-finish-number', 'FinishingWorkController@search_from_finish_number');


    Route::get('carpets-in-sales-office', 'CarpetsController@carpets_in_sales_office');


    // Route::get('finishing-team-carpets/{id}', 'FinishingReceivedController@team_carpets');


    Route::get('/return-to-wash/{id}','FinishingWorkController@return_to_wash');
    Route::get('/return-to-center-from-finish/{carpet_id}','FinishingWorkController@return_to_center');






});
/** End sales and finishing center routes */


/** Sales and Finishing Center Routes */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:SO,CCO,SCO,SP,OM,DE,FI']], function () {

/** Route For Sales */
Route::resource('/sales', 'SaleController');
Route::get('/sales-all', 'SaleController@show_all');
Route::post('/search-carpet-from-sales','SaleController@search');
Route::get('/get_carpet_details.get_by_carpet', 'SaleController@get_carpet_details')->name('dashboard.get_carpet_details.get_by_carpet');



Route::resource('/packing-list', 'PakingListController');
Route::resource('/package-list', 'PackageController');
Route::get('/package_list.get_by_packing', 'PackageController@get_by_packing')->name('dashboard.package_list.get_by_packing');

});

/** Central office Routes */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:CO,CCO,SP,FI']], function () {

        /** Route for Material Accounts */
    Route::resource('/material-accounts', 'MaterialAccountController');
    Route::any('/material-accounts/search', 'MaterialAccountController@search');

    /** Route for Material Account Payments */
    Route::resource('/material-account-payments', 'MaterialAccountPaymentController');

    Route::delete('material-account-approve-request/{id}', 'MaterialAccountPaymentController@approve_request');
    Route::delete('material-account-delete-request/{id}', 'MaterialAccountPaymentController@delete_request');

    Route::get('material-account-request-list', 'MaterialAccountPaymentController@request_material');


    /** Route For Material Category**/
    Route::resource('/material-category', 'MaterialCategoryController');


    Route::get('/agent-payment/{id}', 'AgentsController@agent_payment');

    /** Route For Province **/
    Route::resource('/provinces', 'ProvinceController');

    /** Route For Material Types **/
    Route::resource('/materialtypes', 'MaterialTypeController');

    /** Route For Material Types **/
    Route::resource('/agent-employees', 'AgentEmployeeController');


    // Route for string seller
    Route::resource('/string-seller', 'StringSellerController');
    Route::post('/string-seller/search', 'StringSellerController@search');
    Route::resource('/string-seller-payments', 'SellerPaymentController');
    Route::get('/seller-payments-all/{seller_id}', 'SellerPaymentController@show_all_payment');
    Route::get('/sttring-seller-accounts', 'StringSellerController@accounts');


    /** Route for Material purchase */
    Route::resource('/material-purchase', 'PurchaseMaterialController')->parameters(['material-purchase' => 'purchaseMaterial']);
    Route::get('/material-purchase/search-purchase-number/{purchase_number},{seller_id}', 'PurchaseMaterialController@search_purchase_number');

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


    /** Route for Material stock */
    Route::resource('/material-stock', 'MaterialStockController');
    Route::get('/material-stock/history/{cat}/{type}', 'MaterialStockController@history')->name('material-stock.history');
    Route::post('/material-stock/search', 'MaterialStockController@search');

    /** Route for material sales */
    Route::get('/material-sales/search-sale-number/{sale_number},{agent_id}', 'MaterialSaleController@search_sale_number');
    Route::resource('/material-sales', 'MaterialSaleController');
    Route::get('/material-sales-info', 'MaterialSaleController@get_sale_info')->name('dashboard.material-sales-info');

    /** Route for  Received of Material */
    Route::resource('/carpet-material', 'CarpetMaterialController')->parameters(['carpet-material' => 'material']);
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

    // Route for seller check book
    Route::post('check-book/{id}/close', 'CarpetCheckBookController@closeInvoice')->name('check-book.close');
    Route::resource('/check-book', 'CarpetCheckBookController')->parameters(['check-book' => 'chck']);
    Route::post('check-book/search', 'CarpetCheckBookController@Filter');
    Route::post('check-book/search-agent', 'CarpetCheckBookController@search_agent');
    Route::post('check-book/search-check-number/', 'CarpetCheckBookController@search_check_number');


    // Route for carpet repair
    Route::get('/carpet-repair-create/{id}', 'CarpetRepairController@createRepair');
    Route::resource('/carpet-repair', 'CarpetRepairController');
    Route::get('/carpet-repair/search-kachaee-number/{kachaee_number}{team_id}', 'CarpetRepairController@search_kachaee_number');

    Route::post('/repair-search', 'CarpetRepairController@repair_search');
    Route::post('/search-repaired', 'CarpetRepairController@search_repaired');
    Route::post('/repair-date-search', 'CarpetRepairController@repair_date_search');

    Route::match(['get', 'post'], '/return-to-center-from-non-repair/{id}','CarpetRepairController@return_to_center_from_non_repair');
    Route::match(['get', 'post'], '/return-to-center-from-repair/{id}','CarpetRepairController@return_to_center_from_repair');

    // Agent Carpets Details

    Route::post('/agent-carpet/search', 'AgentsCarpetController@agent_carpet_search');

    Route::get('agent-balance/{carpet}', 'AgentsCarpetController@agent_balance');


    // Route for Kachaee team
    Route::resource('kachaee-team', 'KachaeeController')->parameters(['kachaee-team' => 'team']);
    Route::resource('kachaee-payments', 'KachaeePaymentController');
    Route::get('/kachaee-payments-all/{team_id}', 'KachaeePaymentController@show_all_payment');
    Route::get('/kachaee-accounts', 'KachaeeController@accounts');
    Route::post('/kachaee-team/search', 'KachaeeController@search');

    Route::get('carpet-repaire/sending-to-kachaee/{carpetId}', 'CarpetRepairController@sending_to_repair');
    Route::post('carpet-repair/sent-to-repair/{carpetId}', 'CarpetRepairController@repair_team_selected');


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

/** shared routes */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:CO,SO,SP,SCO,CCO,OM,DE,FI']], function () {


    Route::match(['get', 'post'], '/carpet-wash/sent-to-finish/{carpet}', 'CarpetWashController@sent_to_finishing_center');
    Route::post('/carpet-wash/sent-to-kachaee/{carpet}', 'CarpetWashController@sent_to_kachaee');


    Route::get('washing-team/sending-to-washing/{carpetId}', 'WashingTeamController@sending_to_washing');
    Route::post('washing-team/sent-to-washing/{carpetId}', 'WashingTeamController@washing_team_selected');
    /** Route For Agents**/
    Route::any('/agents/search', 'AgentsController@search');
    Route::resource('/agents', 'AgentsController');
    Route::post('/agent/phone/{id}', 'AgentsController@StorePhone');
    Route::get('/agent/phone/{id}', 'AgentsController@DeletePhone');
    Route::get('/agent-accounts', 'AgentsController@accounts');
    Route::get('/agent-deactive', 'AgentsController@deactive_accounts');
    Route::get('/agent-status-change/{agent_id}', 'AgentsController@change_status');


    /** Route For Phone Book **/
    Route::resource('/phone-book', 'PhoneBookController');
    Route::post('/phone-books/search','PhoneBookController@search');


    /** Route For Carpet Type **/
    Route::resource('/carpet-types', 'CarpetTypeController');

    /** Route For Quality **/
    Route::resource('/carpet-qualities', 'QualityController');

    Route::get('/qualities.get_by_type', 'QualityController@get_by_type')->name('dashboard.qualities.get_by_type');


    /** Route for seller purches */
    Route::resource('/carpet-orders', 'CarpetOrderController');


    Route::resource('/agent-carpet', 'AgentsCarpetController');
    Route::get('/agent-carpets/{id}', 'AgentsCarpetController@carpet_details');


    // RECEIVED OF WASHING TEAM (Controller missing - disabled)
    // Route::resource('received-of-washing', 'ReceivedOfWashingController')->parameters(['received-of-washing' => 'r_washing']);


    // WASHING TEAM ROUTES
    Route::resource('washing-team', 'WashingTeamController')->parameters(['washing-team' => 'team']);
    Route::resource('washing-payments', 'WashingPaymentController');
    Route::get('/washing-payments-all/{team_id}', 'WashingPaymentController@show_all_payment');
    Route::get('/washing-accounts', 'WashingTeamController@accounts');
    Route::post('/washing-team/search', 'WashingTeamController@search');

    Route::get('washing-team-carpets/{id}', 'WashingTeamController@team_carpets');

    /** Route For invoices */
    Route::resource('/invoices', 'InvoiceController');
    Route::post('/invoices/{id}/close', 'InvoiceController@closeInvoice');
    Route::post('/search-invoice','InvoiceController@search');
    Route::get('/invoices/search-invoice-number/{invoice_number},{customer_id}', 'InvoiceController@search_invoice_number');
    Route::post('/search-carpet-from-invoice', 'InvoiceController@search_carpet');

    Route::get('/invoices/sent-to-stock/{id}', 'InvoiceController@sending_to_stock');



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

    Route::get('/all-carpet-edit/{id}', 'DashboardController@all_carpet_edit_dashboard');


});
/**  End shared routes */

/** route for sales center and admin */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:CO,SO,SP,FI']], function () {
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


    /** Route For Employee Payment */
    Route::resource('/expenses', 'OfficeDebitController');
    Route::post('/expenses/search', 'OfficeDebitController@search');
    /** Route For Cash Book List */
    Route::resource('/office-cash-book', 'OfficeCashBookController');
    
    Route::resource('/monthly-expenses','MonthlyExpenseController');
    Route::any('/monthly-expenses-search','MonthlyExpenseController@search');

     Route::resource('/monthly-expense-accounts','NewMonthlyExpenseController');

    Route::resource('/new-monthly-expense-payments','NewMonthlyExpenseBalanceController');
    
    Route::resource('/customer-account-for-orders','CustomerAccountOrderController');
    Route::get('/customer-orders-next-number', 'CustomerOrderController@getNextOrderNumber');
    Route::resource('/customer-orders','CustomerOrderController');
    Route::resource('/customer-order-details','CustomerOrderDetailsController');
    Route::get('/carpet-specification/{id}', 'CustomerOrderDetailsController@show_carpet');
    Route::post('/customer-order-details/{id}/receive', 'CustomerOrderDetailsController@receiveIntoStock');
    Route::post('/customer-order-details/{id}/sell', 'CustomerOrderDetailsController@processFinalSale');
    Route::patch('/customer-order-details/{id}/change-status', 'CustomerOrderDetailsController@changeStatus');

    Route::get('/close-to-end-customer-order','CustomerOrderDetailsController@close_to_end_customer_order');
    
    
     //rout for assets account list
    Route::resource('/assets-accounts','AjnasAccountController');
    Route::resource('/assets-accounts-details','AjnasAccountDetailsController');

    // Assets Accounts Report
    Route::get('/assets-report', 'AssetsReportController@index')->name('assets.report');
    Route::get('/assets-report/excel', 'AssetsReportController@exportExcel')->name('assets.report.excel');
    Route::get('/assets-report/pdf', 'AssetsReportController@exportPdf')->name('assets.report.pdf');



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
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:CCO,SCO,SP,FI']], function () {
    /** Route for carpet customers */
    Route::resource('/customers', 'CustomerController');

    /** Route For customer payments */
    Route::get('/get-outstanding-invoices', 'CustomerPaymentController@get_outstanding_invoices')->name('dashboard.customer_payments.get_outstanding');
    Route::resource('customer-payments', 'CustomerPaymentController');
    
    /** Accounting Reports */
    Route::group(['prefix' => 'reports/accounting'], function () {
        Route::get('/dashboard', 'AccountingReportController@dashboard')->name('accounting.dashboard');
        Route::get('/trial-balance', 'AccountingReportController@trialBalance')->name('accounting.reports.trial-balance');
        Route::get('/income-statement', 'AccountingReportController@incomeStatement')->name('accounting.reports.income-statement');
        Route::get('/ar-aging', 'AccountingReportController@arAging')->name('accounting.reports.ar-aging');
        Route::post('/close-period', 'AccountingReportController@closePeriod')->name('accounting.close_period');
    });
    Route::get('customer-payments-all/{customer_id}', 'CustomerPaymentController@show_all_payment');

    Route::get('/customer-accounts', 'CustomerController@accounts');
    Route::post('/customers/search', 'CustomerController@search');

});

/** Accounting Modules */
Route::group(['prefix' => 'dashboard/accounting', 'middleware' => ['auth', 'usertype:SP,FI']], function () {
    
    /** Dashboard */
    Route::get('/', 'Accounting\DashboardController@index')->name('accounting.dashboard');
    
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
    Route::get('/journals/{id}', 'Accounting\JournalController@show')->name('accounting.journals.show');
    Route::get('/journals/{id}/print', 'Accounting\JournalController@print')->name('accounting.journals.print');
    Route::post('/journals/{id}/reverse', 'Accounting\JournalController@reverse')->name('accounting.journals.reverse');

    /** Mapping Rules */
    Route::get('/mapping-rules', 'Accounting\MappingRuleController@index')->name('accounting.mappings.index');
    Route::post('/mapping-rules', 'Accounting\MappingRuleController@update')->name('accounting.mappings.update');

    /** Warehouse Management */
    Route::resource('/warehouses', 'Accounting\WarehouseController')->names([
        'index' => 'accounting.warehouses.index',
        'store' => 'accounting.warehouses.store',
        'update' => 'accounting.warehouses.update',
        'destroy' => 'accounting.warehouses.destroy',
    ]);
    
    Route::get('/warehouses/{id}/stock-report', 'Accounting\WarehouseController@stockReport')->name('accounting.warehouses.stock_report');
    Route::get('/warehouses/{id}/stock-report/pdf', 'Accounting\WarehouseController@stockReportPdf')->name('accounting.warehouses.stock_report_pdf');
    Route::get('/warehouses/{id}/stock-report/excel', 'Accounting\WarehouseController@stockReportExcel')->name('accounting.warehouses.stock_report_excel');

    /** Warehouse Transfers */
    Route::get('/transfers', 'Accounting\InventoryTransferController@index')->name('accounting.transfers.index');
    Route::get('/transfers/create', 'Accounting\InventoryTransferController@create')->name('accounting.transfers.create');
    Route::post('/transfers', 'Accounting\InventoryTransferController@store')->name('accounting.transfers.store');
    Route::get('/transfers/{id}', 'Accounting\InventoryTransferController@show')->name('accounting.transfers.show');
    Route::post('/transfers/{id}/reverse', 'Accounting\InventoryTransferController@reverse')->name('accounting.transfers.reverse');
    Route::get('/transfers/api/items', 'Accounting\InventoryTransferController@getWarehouseItems')->name('accounting.transfers.api.items');

    /** Warehouse Movements (IN/OUT) */
    Route::get('/warehouse-movements', 'Accounting\WarehouseMovementController@index')->name('accounting.warehouses.movements');

    /** Financial Reports */
    Route::get('/reports/trial-balance', 'Accounting\ReportController@trialBalance')->name('accounting.reports.trial_balance');
    Route::get('/reports/profit-loss', 'Accounting\ReportController@profitLoss')->name('accounting.reports.profit_loss');
    Route::get('/reports/balance-sheet', 'Accounting\ReportController@balanceSheet')->name('accounting.reports.balance_sheet');
    Route::get('/reports/comparative-pl', 'Accounting\ReportController@comparativePL')->name('accounting.reports.comparative_pl');
    Route::get('/reports/inventory-valuation', 'Accounting\ReportController@inventoryValuation')->name('accounting.reports.inventory_valuation');
    Route::get('/reports/fx-exposure', 'Accounting\ReportController@fxExposure')->name('accounting.reports.fx_exposure');
    Route::get('/reports/cost-centers', 'Accounting\ReportController@costCenterPerformance')->name('accounting.reports.cost_center_performance');
    Route::get('/reports/audit-corrections', 'Accounting\ReportController@auditCorrections')->name('accounting.reports.audit_corrections');
    Route::get('/reports/cash-flow', 'Accounting\ReportController@cashFlow')->name('accounting.reports.cash_flow');
    Route::get('/reports/account-ledger', 'Accounting\ReportController@accountLedger')->name('accounting.reports.account_ledger');
    Route::get('/reports/customer-statement', 'Accounting\ReportController@customerStatement')->name('accounting.reports.customer_statement');
    Route::get('/reports/agent-statement', 'Accounting\ReportController@agentStatement')->name('accounting.reports.agent_statement');


    Route::get('/api/allowed-accounts', 'Accounting\AccountController@getAllowedAccounts')->name('accounting.api.allowed_accounts');
});
/**  */

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:CCO,SCO,CO,SO,SP,MO,FI']], function () {
//    Route::get('/', 'DashboardController@Index');
    /** Route for different account */
    Route::resource('/different-account', 'DifferentAccountController');
    Route::get('different-account-payments-all/{account_id}', 'DifferentAccountController@show_all_payment');
    Route::post('different-account/search', 'DifferentAccountController@search');

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
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:CCO,SCO,SP,MO,SO,CO,PH,OM,DE,FI']], function () {
    Route::get('/', 'EnterpriseDashboardController@index');
    Route::get('/production', 'EnterpriseDashboardController@production');
    Route::get('/inventory', 'EnterpriseDashboardController@inventory');
    Route::get('/sales', 'EnterpriseDashboardController@sales');
    Route::get('/purchases', 'EnterpriseDashboardController@purchases');
    Route::get('/finance', 'EnterpriseDashboardController@finance');
    Route::get('/cost-analytics', 'EnterpriseDashboardController@costAnalytics');
    Route::get('/all-carpet-dashboard-show-all', 'DashboardController@show_all');
    Route::post('/all-carpet-dashboard/search', 'DashboardController@search_all_carpet_dashboard');
      Route::any('/search-this-month-carpet','DashboardController@search_this_month_carpet');
    Route::get('/all-exesting-carpet', 'DashboardController@show_existing_carpet');
    
    
    
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

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'usertype:CCO,CO,SP,AO,FI']], function () {
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
    Route::get('/accounting/reports/different-account-statement', 'Accounting\ReportController@differentAccountStatement')->name('accounting.reports.different_account_statement');
    Route::get('/accounting/reports/repair-team-statement', 'Accounting\ReportController@repairTeamStatement')->name('accounting.reports.repair_team_statement');
    Route::get('/accounting/reports/washing-team-statement', 'Accounting\ReportController@washingTeamStatement')->name('accounting.reports.washing_team_statement');
    Route::get('/accounting/reports/finishing-team-statement', 'Accounting\ReportController@finishingTeamStatement')->name('accounting.reports.finishing_team_statement');
    Route::get('/accounting/reports/string-seller-statement', 'Accounting\ReportController@stringSellerStatement')->name('accounting.reports.string_seller_statement');
    Route::get('/accounting/reports/employee-statement', 'Accounting\ReportController@employeeStatement')->name('accounting.reports.employee_statement');
    Route::get('/batches/{type}', 'ProductionBatchController@index')->name('batches.index');
    Route::post('/batches/{type}', 'ProductionBatchController@store')->name('batches.store');
    Route::post('/batches/{id}/toggle-status', 'ProductionBatchController@toggleStatus')->name('batches.toggle-status');
    Route::get('/batches/{id}/details', 'ProductionBatchController@details')->name('batches.details');
});

Auth::routes();
Route::redirect('/', '/login');

Route::fallback(function () {
    abort(404);
});


Route::get('/logout', function () {
    
     $activity = new Activity();
      $activity->date = \Carbon\Carbon::today()->format('Y-m-d');
    $activity->description =  "استفاده کننده بنام ". Auth::user()->name ." از سیستم خارج شد ";
    $activity->user_id = Auth::user()->id;
    $activity->save();

    
    auth()->logout();
    Session()->flush();
    return Redirect::to('/');
})->name('logout');

Route::get('/test-missing-column', function () {
    return \Illuminate\Support\Facades\DB::select("select non_existent_column from carpets");
});
