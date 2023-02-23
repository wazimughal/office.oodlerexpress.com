<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminpanel\DashboardController;
use App\Http\Controllers\adminpanel\AdminController;
use App\Http\Controllers\adminpanel\LoginController;




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

  
Route::group(['prefix' => 'admin'], function () {

    Auth::routes();

});


// This is all for testing
Route::get('add-item',[App\Http\Controllers\adminpanel\quickbooks::class,'add_quickbooks_item'])->name('customer.add_quickbooks_item');
Route::get('add-sales',[App\Http\Controllers\adminpanel\quickbooks::class,'add_quickbooks_sales'])->name('customer.add_quickbooks_sales');
Route::get('add-sales-new',[App\Http\Controllers\adminpanel\quickbooks::class,'add_quickbooks_new_sales'])->name('customer.add_quickbooks_new_sales');
Route::get('make-payment',[App\Http\Controllers\adminpanel\quickbooks::class,'makePayment'])->name('customer.makepayment');
Route::get('create-customer',[App\Http\Controllers\adminpanel\quickbooks::class,'createCustomer'])->name('create.customer');
Route::get('receive-payment',[App\Http\Controllers\adminpanel\quickbooks::class,'receive_payment'])->name('qb.receive_payment');
Route::get('quickbook_test',[App\Http\Controllers\adminpanel\quickbooks::class,'quickbook_test_new'])->name('qb.quickbook_test');
Route::get('/admin/send-sms',[App\Http\Controllers\adminpanel\QuotesController::class,'sendSMS'])->name('previous.sendSMS');

// Route::get('/clearroute', function () {

//     $exitCode = Artisan::call('route:cache');

//     return "route Cache Cleared!";

// });


use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;


Route::get('sendemail', function(){
    $mailData['body_message'] = 'Body Message';
    $mailData['subject'] = 'Subject here';
    
     $mailData['body_message'] = quote_data_for_mail(2);
    
    return view('emails.delivered', compact('mailData'));
    return view('emails.email_template', compact('mailData'));
    return view('emails.booking_email_template', compact('mailData'));
    
    if(Mail::to("waximarshad@outlook.com")->send(new EmailTemplate($mailData)))
    dd("Mail Sent Successfully!");
    else
    dd('Sending Failed');
});

Route::get('clearcache', function () {
    $exitCode = Artisan::call('config:cache');
    $exitCode1 = Artisan::call('config:clear');
    $exitCode2 = Artisan::call('cache:clear');
    $exitCode3 = Artisan::call('route:cache');

    return "View Cache Cleared!";
});
Route::get('migrate-fresh', function () {
    $exitCode3 = Artisan::call('migrate:fresh');

    return "data migrated";
});
Route::get('migrate', function () {
    $exitCode3 = Artisan::call('migrate');

    return " migrated";
});

Route::get('command', function () {
    if(isset($_GET['c']) && $_GET['c']!='')
   {
   // echo $_GET['c'];die;
    
    $exitCode3 = Artisan::call($_GET['c']);
    return " executed";
   }
   else{
    return 'command not set';
   } 

    
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin/login/', [AdminController::class,'login'])->name('admin.loginform');
Route::post('/admin/login/', [AdminController::class,'authenticate'])->name('admin.loginpost');
Route::get('/admin/register/', [AdminController::class,'index'])->name('admin.registerform');
Route::post('/admin/register/', [AdminController::class,'register'])->name('admin.registerpost');
Route::get('/admin/logout/', [AdminController::class,'logout'])->name('admin.logout');
Route::get('/reload-captcha', [AdminController::class, 'reloadCaptcha'])->name('reloadCaptcha');;




Route::middleware(['adminCustomerGaurd'])->group(function () { 

});

Route::middleware(['adminGaurd'])->group(function () {   

    Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin.dashboard');
// Lead Management 

Route::get('/admin/leads',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('admin.leads');
Route::get('/admin/lead/{type?}',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('admin.lead');
Route::get('/admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'addLeads'])->name('admin.leadsform');
Route::get('/admin/leads/view/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'view_leads'])->name('admin.leadview');
Route::get('/admin/leads/edit/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'editLeads'])->name('admin.leadseditform');
Route::post('/admin/leads/edit/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'save_editLeads'])->name('admin.leadseditsave');
Route::get('/admin/leads/add-to-customer/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'add_to_customer'])->name('admin.add_to_customer');
Route::post('/admin/leads/add-to-customer/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'save_add_to_customer'])->name('admin.save_add_to_customer');
Route::post('admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'save_new_lead'])->name('admin.leads.save');
Route::any('admin/leads/ajaxcall/{id?}',[App\Http\Controllers\adminpanel\LeadsController::class,'ajaxcall'])->name('leads.ajaxcall');
// Customer Trashed
Route::get('/admin/customer/{type?}',[App\Http\Controllers\adminpanel\CustomersController::class,'customers'])->name('admin.customer');

// Color Management 
Route::get('/admin/colors',[App\Http\Controllers\adminpanel\ColorsController::class,'colors'])->name('colors');
//Route::get('/admin/colors/add',[App\Http\Controllers\adminpanel\ColorsController::class,'addcolors'])->name('colors.addfomr');
Route::post('/admin/colors',[App\Http\Controllers\adminpanel\ColorsController::class,'SavecolorsData'])->name('colors.add');
Route::any('admin/colors/ajaxcall/{id}',[App\Http\Controllers\adminpanel\ColorsController::class,'ajaxcall'])->name('colors.ajaxcall');


// Product Management
Route::get('/admin/products/categories',[App\Http\Controllers\adminpanel\ProductsController::class,'categoreis'])->name('admin.categories'); 
Route::get('/admin/products',[App\Http\Controllers\adminpanel\ProductsController::class,'products'])->name('admin.products');
Route::get('/admin/products/add',[App\Http\Controllers\adminpanel\ProductsController::class,'addproducts'])->name('products.openform');
Route::post('admin/products/add',[App\Http\Controllers\adminpanel\ProductsController::class,'add_new_product'])->name('products.add');
Route::any('admin/products/ajaxcall/{id}',[App\Http\Controllers\adminpanel\ProductsController::class,'ajaxcall'])->name('products.ajaxcall');
Route::any('admin/products/categoryajaxcall/{id?}',[App\Http\Controllers\adminpanel\ProductsController::class,'categoryajaxcall'])->name('pro_category.ajaxcall');

// Users Management
Route::get('/admin/users',[App\Http\Controllers\adminpanel\AdminController::class,'users'])->name('admin.users');
Route::get('/admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'addUser'])->name('admin.usersformadd');
Route::post('admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'add_user_data'])->name('admin.users.save');
Route::get('/admin/users/edit/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'edit_user_form'])->name('admin.edit_user_form');
Route::post('admin/users/edit/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'update_user_data'])->name('admin.update_user_data');
//Route::any('admin/users/delete/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'lead'])->name('admin.users.delete');
Route::any('admin/users/changestatus/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'changeStatus'])->name('admin.users.changestatus');
Route::get('/admin/activity-log',[App\Http\Controllers\adminpanel\AdminController::class,'activitylog'])->name('admin.activitylog');

});

Route::middleware(['roleGaurd'])->group(function () {

// Customers Management 

Route::get('/admin/customers',[App\Http\Controllers\adminpanel\CustomersController::class,'customers'])->name('admin.customers');
Route::get('/admin/customers/edit/{id}',[App\Http\Controllers\adminpanel\CustomersController::class,'editcustomer'])->name('admin.customerseditform');
Route::post('/admin/customers/edit/{id}',[App\Http\Controllers\adminpanel\CustomersController::class,'save_edit_customer'])->name('admin.save_edit_customer');
Route::get('/admin/customers/add',[App\Http\Controllers\adminpanel\CustomersController::class,'addcustomers'])->name('admin.customersaddform');
Route::post('admin/customers/add',[App\Http\Controllers\adminpanel\CustomersController::class,'save_new_customer'])->name('admin.customers.save');
Route::get('/admin/customer/quotes/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'customer_quotes'])->name('customer.quotes');
Route::any('admin/customers/ajaxcall/{id}',[App\Http\Controllers\adminpanel\CustomersController::class,'ajaxcall'])->name('admin.customers.ajaxcall');


// Payment Management 
Route::get('/delivery/open-balance',[App\Http\Controllers\adminpanel\QuotesController::class,'open_balance_deliveries'])->name('open_balance_deliveries');
Route::any('/deliveries/make-payments/{customer_id}',[App\Http\Controllers\adminpanel\QuotesController::class,'make_deliveries_payments'])->name('make_deliveries_payments');
// Quotes Management 
Route::get('/admin/quotes',[App\Http\Controllers\adminpanel\QuotesController::class,'quotes'])->name('admin.quotes');
Route::get('/admin/quote/{type?}',[App\Http\Controllers\adminpanel\QuotesController::class,'quotes'])->name('admin.quote.types');
Route::get('/admin/quotes/request/{id?}',[App\Http\Controllers\adminpanel\QuotesController::class,'request_quotes_form'])->name('quotes.request_quotes_form');
Route::post('admin/quotes/request/{id?}',[App\Http\Controllers\adminpanel\QuotesController::class,'save_quote_data'])->name('quotes.save_quote_data');
Route::get('/admin/quotes/edit/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'quotes_edit_form'])->name('quotes.quoteeditform');
Route::post('admin/quotes/edit/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'save_quote_edit'])->name('quotes.save_quote_edit');
Route::get('/admin/quotes/view/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'view_quote'])->name('quotes.view');
Route::get('admin/quotes/send/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'send_quote_form'])->name('quotes.send_quote_form');
Route::post('admin/quotes/send/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'send_quote_data'])->name('quotes.send_quote_data');
Route::get('admin/quotes/add-to-delivery/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'add_to_delivery'])->name('quotes.add_to_delivery_form');
Route::post('admin/quotes/add-to-delivery/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'save_add_to_delivery'])->name('quotes.add_to_delivery_save');
Route::any('/admin/quotes/upload_quote_request',[App\Http\Controllers\adminpanel\QuotesController::class,'upload_quote_request'])->name('quote.upload_request');
Route::any('/admin/quotes/requested_documents/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'quote_requested_documents'])->name('quote_requested_documents');
Route::any('admin/quotes/ajaxcall/{id?}',[App\Http\Controllers\adminpanel\QuotesController::class,'ajaxcall'])->name('quotes.ajaxcall');

//Reports
Route::get('admin/reports/quote-delivery',[App\Http\Controllers\adminpanel\QuotesController::class,'report_quote_delivery'])->name('quotes.deliveries');
Route::get('admin/reports/drivers',[App\Http\Controllers\adminpanel\DriverController::class,'report_drivers'])->name('driver.reports');
Route::get('admin/reports/subs',[App\Http\Controllers\adminpanel\DriverController::class,'report_subs'])->name('subs.reports');
Route::get('admin/reports/customers',[App\Http\Controllers\adminpanel\CustomersController::class,'report_customers'])->name('customer.reports');
Route::get('/reports/export-customer-delivery-balance',[App\Http\Controllers\adminpanel\CustomersController::class,'report_customer_delivery_balance'])->name('customer.report_customer_delivery_balance');
Route::get('/reports/export-driver-working-hours',[App\Http\Controllers\adminpanel\DriverController::class,'report_driver_working_hours'])->name('driver.report_driver_working_hours');
Route::get('/reports/export-sub-delivery-balance',[App\Http\Controllers\adminpanel\DriverController::class,'report_sub_delivery_balance'])->name('sub.report_sub_delivery_balance');
Route::get('/reports/export-quotes',[App\Http\Controllers\adminpanel\QuotesController::class,'report_export_quote_delivery'])->name('quotes.deliveries.export');

// Deliveries Management
Route::get('/admin/delivery/add/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'add_delivery_form'])->name('delivery.add_delivery_form');
Route::post('admin/delivery/add/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'save_new_delivery_data'])->name('delivery.save_delivery_data');
Route::get('/admin/delivery/edit/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'delivery_edit_form'])->name('delivery.editform');
Route::post('admin/delivery/edit/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'save_delivery_edit'])->name('delivery.save_delivery_edit');

Route::get('/admin/scheduled-deliveries',[App\Http\Controllers\adminpanel\QuotesController::class,'deliveries'])->name('scheduled.deliveries');
Route::get('/admin/previous-deliveries',[App\Http\Controllers\adminpanel\QuotesController::class,'previous_deliveries'])->name('previous.deliveries');

Route::get('/admin/deliveries',[App\Http\Controllers\adminpanel\QuotesController::class,'deliveries'])->name('admin.deliveries');
Route::any('/admin/deliveries/view/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'view_delivery'])->name('deliveries.view');
Route::any('/admin/deliveries/upload_proof/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'upload_delivery_proof'])->name('delivery.upload_proof');
Route::any('/admin/deliveries/uploade_document_for_driver/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'uploade_documents_for_driver'])->name('delivery.uploade_document_for_driver');
Route::get('/delivery/downloadpdf',[App\Http\Controllers\adminpanel\QuotesController::class,'download_pdf_deliery'])->name('download_pdf_deliery');
Route::get('/deliveries/calender',[App\Http\Controllers\adminpanel\QuotesController::class,'calender_schedule'])->name('user.calender');

// Invoices
Route::get('/admin/deliveries/invoice/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'invoice_delivery'])->name('delivery.invoice');
Route::get('/admin/deliveries/invoice/customer/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'invoice_delivery'])->name('download.customer.invoice');
Route::get('/admin/deliveries/invoice/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'invoice_delivery'])->name('download.invoice');
Route::get('/admin/deliveries/send-invoice/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'send_delivery_invoice'])->name('send.customer.invoice');
Route::post('admin/deliveries/add_invoice/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'save_delivery_invoice_data'])->name('delivery.add_invoice');

// Driver Management 
Route::get('/admin/drivers',[App\Http\Controllers\adminpanel\DriverController::class,'drivers'])->name('admin.drivers');
Route::get('admin/driver/{type?}',[App\Http\Controllers\adminpanel\DriverController::class,'drivers'])->name('drivers.type');
Route::get('/admin/drivers/add-documents/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'add_documents'])->name('drivers.add-documents');
Route::any('/admin/drivers/upload-documents/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'upload_documents'])->name('drivers.uploaddocuments');

// For Testing Purpose
//Route::any('dropzone/store/{id?}', [App\Http\Controllers\adminpanel\DriverController::class,'upload_documents'])->name('dropzone.store');
Route::get('/admin/drivers/add',[App\Http\Controllers\adminpanel\DriverController::class,'adddrivers'])->name('drivers.openform');
Route::post('admin/drivers/add',[App\Http\Controllers\adminpanel\DriverController::class,'add_new_driver'])->name('drivers.add');
Route::get('/admin/drivers/edit/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'edit_driver'])->name('drivers.open_edit_form');
Route::post('admin/drivers/edit/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'save_edit_driver'])->name('drivers.edit');
Route::any('admin/drivers/ajaxcall/{id?}',[App\Http\Controllers\adminpanel\DriverController::class,'ajaxcall'])->name('drivers.ajaxcall');
// subs Management 
Route::get('/admin/subs',[App\Http\Controllers\adminpanel\DriverController::class,'subs'])->name('admin.subs');
Route::get('admin/sub/{type?}',[App\Http\Controllers\adminpanel\DriverController::class,'subs'])->name('subs.type');
Route::get('/admin/subs/add',[App\Http\Controllers\adminpanel\DriverController::class,'addsubs'])->name('subs.openform');
Route::post('admin/subs/add',[App\Http\Controllers\adminpanel\DriverController::class,'add_new_sub'])->name('subs.add');
Route::get('/admin/subs/edit/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'edit_sub'])->name('subs.open_edit_form');
Route::post('admin/subs/edit/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'save_edit_sub'])->name('subs.edit');
Route::any('admin/subs/ajaxcall/{id?}',[App\Http\Controllers\adminpanel\DriverController::class,'ajaxcall'])->name('subs.ajaxcall');


});

// Approve or Reject by The Customer
Route::any('/customer/quote/action/{quote_id}/{action}',[App\Http\Controllers\adminpanel\QuotesController::class,'customer_action'])->name('customer_action');
Route::any('/sub/quote/action/{quote_id}/{action}',[App\Http\Controllers\adminpanel\QuotesController::class,'sub_action'])->name('sub_action');
Route::any('/driver/quotes-action/{id}/{action?}',[App\Http\Controllers\adminpanel\DriverController::class,'driver_action_taken'])->name('driver_action_taken');
Route::any('/admin/drivers/files-documents/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'driver_action_files'])->name('drivers.driver_action_files');

Route::any('delete_quotes_without_po_number_cron',[App\Http\Controllers\adminpanel\QuotesController::class,'delete_quotes_without_po_number'])->name('delete_quotes_without_po_number');


Route::get('/admin/no-access/', function(){
    echo 'you are not allowed to access the page ! ONLY the admins are allowed';
    //return redirect('/admin/login');
    die;
});
Route::get('/hod/no-access/', function(){
    echo 'you are not allowed to access the page ! ONLY the Hod are allowed';
    //return redirect('/admin/login');
    die;
});

Route::get('/', function () {
    return redirect()->route('admin.loginform');
    return view('welcome');
});


Route::get('/test', function () {
    $_POST['capital']='100.000';
    echo $_POST['capital']=str_replace('.','',$_POST['capital']);
});