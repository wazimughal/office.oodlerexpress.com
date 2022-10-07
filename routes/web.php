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
Auth::routes();
// Route::group(['prefix' => 'admin'], function () {

//     Auth::routes();

// });
// Route::get('/clearroute', function () {

//     $exitCode = Artisan::call('route:cache');

//     return "route Cache Cleared!";

// });


use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;


Route::get('sendemail', function(){
    $mailData = [
        "name" => "Test NAME",
        "dob" => "12/12/1993",
        
    ];
    
    return view('emails.email_template');
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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin/login/', [AdminController::class,'login'])->name('admin.loginform');
Route::post('/admin/login/', [AdminController::class,'authenticate'])->name('admin.loginpost');
Route::get('/admin/register/', [AdminController::class,'index'])->name('admin.registerform');
Route::post('/admin/register/', [AdminController::class,'register'])->name('admin.registerpost');
Route::get('/admin/logout/', [AdminController::class,'logout'])->name('admin.logout');


Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin.dashboard');

Route::middleware(['adminCustomerGaurd'])->group(function () { 

});

Route::middleware(['adminGaurd'])->group(function () {   


// Lead Management 

Route::get('/admin/leads',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('admin.leads');
Route::get('/admin/lead/{type?}',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('admin.lead');
Route::get('/admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'addLeads'])->name('admin.leadsform');
Route::get('/admin/leads/edit/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'editLeads'])->name('admin.leadseditform');
Route::post('/admin/leads/edit/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'save_editLeads'])->name('admin.leadseditsave');
Route::get('/admin/leads/add-to-customer/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'add_to_customer'])->name('admin.add_to_customer');
Route::post('/admin/leads/add-to-customer/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'save_add_to_customer'])->name('admin.save_add_to_customer');
Route::post('admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'SaveUsersData'])->name('admin.leads.save');
Route::any('admin/leads/ajaxcall/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'ajaxcall'])->name('leads.changestatus');
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
Route::post('admin/products/add',[App\Http\Controllers\adminpanel\ProductsController::class,'SaveproductsData'])->name('products.add');
Route::any('admin/products/ajaxcall/{id}',[App\Http\Controllers\adminpanel\ProductsController::class,'ajaxcall'])->name('products.ajaxcall');
Route::any('admin/products/categoryajaxcall/{id?}',[App\Http\Controllers\adminpanel\ProductsController::class,'categoryajaxcall'])->name('pro_category.ajaxcall');

// Users Management
Route::get('/admin/users',[App\Http\Controllers\adminpanel\AdminController::class,'users'])->name('admin.users');
Route::get('/admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'addUser'])->name('admin.usersformadd');
Route::post('admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'SaveUsersData'])->name('admin.users.save');
Route::any('admin/users/update/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'UpdateUsersData'])->name('admin.users.update');
Route::any('admin/users/delete/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'DeleteUsersData'])->name('admin.users.delete');
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
Route::any('admin/customers/ajaxcall/{id}',[App\Http\Controllers\adminpanel\CustomersController::class,'ajaxcall'])->name('admin.customers.ajaxcall');

// Quotes Management 

Route::get('/admin/quotes',[App\Http\Controllers\adminpanel\QuotesController::class,'quotes'])->name('admin.quotes');
Route::get('/admin/quote/{type?}',[App\Http\Controllers\adminpanel\QuotesController::class,'quotes'])->name('admin.quote.types');
Route::get('/admin/quotes/request/{id?}',[App\Http\Controllers\adminpanel\QuotesController::class,'request_quotes_form'])->name('quotes.request_quotes_form');
Route::post('admin/quotes/request/{id?}',[App\Http\Controllers\adminpanel\QuotesController::class,'save_quote_data'])->name('quotes.save_quote_date');
Route::get('/admin/quotes/eidt/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'quotes_edit_form'])->name('quotes.quoteeditform');
Route::post('admin/quotes/edit/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'save_quote_edit'])->name('quotes.save_quote_edit');
Route::get('admin/quotes/send/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'send_quote_form'])->name('quotes.send_quote_form');
Route::post('admin/quotes/send/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'send_quote_data'])->name('quotes.send_quote_data');
Route::get('admin/quotes/add-to-delivery/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'add_to_delivery'])->name('quotes.add_to_delivery_form');
Route::post('admin/quotes/add-to-delivery/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'save_add_to_delivery'])->name('quotes.add_to_delivery_save');
Route::any('admin/quotes/ajaxcall/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'ajaxcall'])->name('quotes.ajaxcall');

// Deliveries Management
Route::get('/admin/deliveries',[App\Http\Controllers\adminpanel\QuotesController::class,'deliveries'])->name('admin.deliveries');
Route::get('/admin/deliveries/view/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'view_delivery'])->name('deliveries.view');


Route::get('/deliveries/calender',[App\Http\Controllers\adminpanel\QuotesController::class,'calender_schedule'])->name('user.calender');


// Driver Management 
Route::get('/admin/drivers',[App\Http\Controllers\adminpanel\DriverController::class,'drivers'])->name('admin.drivers');
Route::get('drivers/{type?}',[App\Http\Controllers\adminpanel\DriverController::class,'drivers'])->name('drivers.trashed');
Route::get('/admin/drivers/add-documents/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'add_documents'])->name('drivers.add-documents');
Route::any('/admin/drivers/upload-documents/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'upload_documents'])->name('drivers.uploaddocuments');
// For Testing Purpose
//Route::any('dropzone/store/{id?}', [App\Http\Controllers\adminpanel\DriverController::class,'upload_documents'])->name('dropzone.store');
Route::get('/admin/drivers/add',[App\Http\Controllers\adminpanel\DriverController::class,'adddrivers'])->name('drivers.openform');
Route::post('admin/drivers/add',[App\Http\Controllers\adminpanel\DriverController::class,'add_new_driver'])->name('drivers.add');
Route::get('/admin/drivers/edit/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'edit_driver'])->name('drivers.open_edit_form');
Route::post('admin/drivers/edit/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'save_edit_driver'])->name('drivers.edit');
Route::any('admin/drivers/ajaxcall/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'ajaxcall'])->name('drivers.ajaxcall');


});

// Approve or Reject by The Customer
Route::any('/customer/quote/action/{quote_id}/{action}',[App\Http\Controllers\adminpanel\QuotesController::class,'customer_action'])->name('customer_action');


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
    return view('welcome');
});


Route::get('/test', function () {
    $_POST['capital']='100.000';
    echo $_POST['capital']=str_replace('.','',$_POST['capital']);
});