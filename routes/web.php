<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminpanel\DashboardController;
use App\Http\Controllers\adminpanel\AdminController;
use App\Http\Controllers\adminpanel\AdminLabTestsController;
use App\Http\Controllers\adminpanel\OrganizationsController;
use App\Http\Controllers\adminpanel\LoginController;
use App\Http\Controllers\adminpanel\PatientReportsController;



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
Route::get('clearcache', function () {
    $exitCode = Artisan::call('config:cache');
    $exitCode1 = Artisan::call('config:clear');
    $exitCode2 = Artisan::call('cache:clear');
    $exitCode3 = Artisan::call('route:cache');

    return "View Cache Cleared!";
});

Route::resource('/admin/patient-reports', PatientReportsController::class)->except([
    'store'
])->middleware('adminHodGaurd');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin/login/', [AdminController::class,'login'])->name('admin.loginform');
Route::post('/admin/login/', [AdminController::class,'authenticate'])->name('admin.loginpost');
Route::get('/admin/register/', [AdminController::class,'index'])->name('admin.registerform');
Route::post('/admin/register/', [AdminController::class,'register'])->name('admin.registerpost');
Route::get('/admin/logout/', [AdminController::class,'logout'])->name('admin/logout/');


// Route::middleware(['roleGaurd'])->group(function () {
// Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin/dashboard/{id?}');
// });

Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin/dashboard/{id?}');

    
    

Route::middleware(['adminHodGaurd'])->group(function () {

// Lead Management 

Route::get('/admin/leads',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('/admin.leads');
Route::get('/admin/lead/{type?}',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('admin.lead');
Route::get('/admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'addLeads'])->name('/admin/leads/add');
Route::post('admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'SaveUsersData'])->name('admin/leads/add');
Route::any('admin/leads/ajaxcall/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'ajaxcall'])->name('admin/leads/changestatus/{id}');

// Customers Management 

Route::get('/admin/customers',[App\Http\Controllers\adminpanel\CustomersController::class,'customers'])->name('/admin/customers');
Route::get('/admin/customers/add',[App\Http\Controllers\adminpanel\CustomersController::class,'addcustomers'])->name('/admin/customers/add');
Route::post('admin/customers/add',[App\Http\Controllers\adminpanel\CustomersController::class,'SaveCustomersData'])->name('admin/customers/add');
Route::any('admin/customers/ajaxcall/{id}',[App\Http\Controllers\adminpanel\CustomersController::class,'ajaxcall'])->name('admin/customers/changestatus/{id}');

// Color Management 
Route::get('/admin/colors',[App\Http\Controllers\adminpanel\ColorsController::class,'colors'])->name('colors');
//Route::get('/admin/colors/add',[App\Http\Controllers\adminpanel\ColorsController::class,'addcolors'])->name('colors.addfomr');
Route::post('/admin/colors',[App\Http\Controllers\adminpanel\ColorsController::class,'SavecolorsData'])->name('colors.add');
Route::any('admin/colors/ajaxcall/{id}',[App\Http\Controllers\adminpanel\ColorsController::class,'ajaxcall'])->name('colors.ajaxcall');

// Quotes Management 

Route::get('/admin/quotes',[App\Http\Controllers\adminpanel\QuotesController::class,'quotes'])->name('admin.quotes');
Route::get('/admin/quote/{type?}',[App\Http\Controllers\adminpanel\QuotesController::class,'quotes'])->name('admin.quote.types');
Route::get('/admin/quotes/request',[App\Http\Controllers\adminpanel\QuotesController::class,'addquotes'])->name('admin.quotes.reqform');
Route::post('admin/quotes/add',[App\Http\Controllers\adminpanel\QuotesController::class,'SaveUsersData'])->name('admin.quotes.add');
Route::any('admin/quotes/ajaxcall/{id}',[App\Http\Controllers\adminpanel\QuotesController::class,'ajaxcall'])->name('quotes.ajaxcall');

// Driver Management 
Route::get('/admin/drivers',[App\Http\Controllers\adminpanel\DriverController::class,'drivers'])->name('/admin/drivers');
Route::get('/admin/drivers/add-documents/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'add_documents'])->name('drivers.add-documents');
Route::any('/admin/drivers/upload-documents/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'upload_documents'])->name('drivers.uploaddocuments');
// For Testing Purpose
Route::any('dropzone/store/{id?}', [App\Http\Controllers\adminpanel\DriverController::class,'upload_documents'])->name('dropzone.store');

Route::get('/admin/drivers/add',[App\Http\Controllers\adminpanel\DriverController::class,'adddrivers'])->name('drivers.openform');
Route::post('admin/drivers/add',[App\Http\Controllers\adminpanel\DriverController::class,'SavedriversData'])->name('drivers.add');
Route::any('admin/drivers/ajaxcall/{id}',[App\Http\Controllers\adminpanel\DriverController::class,'ajaxcall'])->name('drivers.ajaxcall');

//echo 'echo'. config('constants.groups.staff');
//echo '<br>echasdo'. config('constants.groups.subscriber'); die;
// Users Management
Route::get('/admin/users',[App\Http\Controllers\adminpanel\AdminController::class,'users'])->name('/admin/users');
Route::get('/admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'addUser'])->name('/admin/users/add');
Route::post('admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'SaveUsersData'])->name('admin/users/add');
Route::any('admin/users/update/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'UpdateUsersData'])->name('admin/users/update/{id}');
Route::any('admin/users/delete/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'DeleteUsersData'])->name('admin/users/delete/{id}');
Route::any('admin/users/changestatus/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'changeStatus'])->name('admin/users/changestatus/{id}');
Route::get('/admin/activity-log',[App\Http\Controllers\adminpanel\AdminController::class,'activitylog'])->name('/admin/activitylog');
Route::get('/admin/calender',[App\Http\Controllers\adminpanel\AdminController::class,'calenderSchedule'])->name('user.calender');



});

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