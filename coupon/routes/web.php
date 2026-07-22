<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductmodelController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductSerialController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OtherProductSerialController;
use App\Http\Controllers\Admin\FrontController;
use App\Http\Controllers\ProductRegistrationController;
use App\Http\Controllers\ProductComplainController;
use App\Http\Controllers\ProductFeedbackController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Models\OtherProduct;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


// Route::post('/logout', function () {
//     Auth::logout();
//     return redirect('/login');
// });




// Route::post('/logout', function () {
//     Auth::logout();
//     Session::flush();
//     return redirect('/');
// });

// Route::group(['middleware' => ['web']], function () {
//     Route::post('/logout', function () {
//         Auth::logout();
//         Session::flush();
//         return redirect('/login');
//     });
// });

//use App\Http\Controllers\Auth\RegisterController;

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

//Route::get('/', function () {
//    return view('welcome');
//});
//Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::get('/admin/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
//Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');


// session()->flush();


Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();





Route::get('/findproduct/{serialId}', [App\Http\Controllers\FrontController::class, 'findproduct'])->name('findproduct');

Route::resource('productregistration', ProductRegistrationController::class);

Route::get('productregistrationsuccess', [ProductRegistrationController::class, 'registration_success'])->name('registration_success');

Route::resource('productcomplain', ProductComplainController::class);

Route::resource('product_feedback', ProductFeedbackController::class);






//Route::group(['middleware' => ['role:super-admin|admin']], function () {
//
//    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
//    Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy']);
//
//    Route::resource('roles', App\Http\Controllers\RoleController::class);
//    Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy']);
//    Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'addPermissionToRole']);
//    Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'givePermissionToRole']);
//
//    Route::resource('users', App\Http\Controllers\UserController::class);
//    Route::get('users/{userId}/delete', [App\Http\Controllers\UserController::class, 'destroy']);
//});




Route::group(['middleware' => ['role:super-admin|admin|user'], 'auth', 'prefix' => 'admin'], function () {

// Route::group(['prefix' => 'admin'], function () {



    Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');


    Route::resource('permissions', PermissionController::class);
    //    Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy']);

    Route::resource('roles', RoleController::class);
    //    Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy']);

    Route::get('roles/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole'])->name('addPermissionToRole');
    Route::put('roles/{roleId}/give-permissions', [RoleController::class, 'givePermissionToRole'])->name('givePermissionToRole');

    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::get('users/{userId}/delete', [App\Http\Controllers\UserController::class, 'destroy']);

    //  ========================================  

    Route::get('/visitors', [App\Http\Controllers\VisitorController::class, 'index'])->name('visitors');


    Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');




    Route::resource('couponserials', CouponController::class);
    Route::POST('couponserialcode', [CouponController::class, 'serialcodePrint'])->name('couponcodeprint');

    Route::post('rangecouponserialcode', [CouponController::class, 'rangeserialcodePrint'])->name('rangecouponcodeprint');


    Route::post('/admin/couponserials/status/{id}', [CouponController::class, 'updateStatus'])->name('admin.couponserials.updateStatus');




    Route::resource('products', ProductController::class);

    Route::get('other_product_list', [ProductController::class, 'otherProductList'])->name('other_product_list');
    Route::get('other_product_add', [ProductController::class, 'otherProductAdd'])->name('other_product_add');
    Route::get('other_product_view/{id}', [ProductController::class, 'otherProductView'])->name('other_product_view');
    Route::post('other_product_store', [ProductController::class, 'otherProductStore'])->name('other_product_store');

    Route::get('other_product_edit/{id}', [ProductController::class, 'otherProductEdit'])->name('other_product_edit');
    Route::post('other_product_update/{id}', [ProductController::class, 'otherProductUpdate'])->name('other_product_update');


    Route::get('other_product_serial_list', [OtherProductSerialController::class, 'index'])->name('other_product_serial_list');
    Route::get('other_product_serial_add', [OtherProductSerialController::class, 'create'])->name('other_product_serial_add');

    Route::post('other_product_serial_store', [OtherProductSerialController::class, 'Store'])->name('other_product_serial_store');
    Route::POST('other_productserialcode', [OtherProductSerialController::class, 'otherSerialcodePrint'])->name('other_productserialcode');
    Route::post('other_rangeproductserialcode', [OtherProductSerialController::class, 'otherRangeserialcodePrint'])->name('other_rangeproductserialcode');




    Route::get('product_image_destroy/{id}', [ProductController::class, 'imageDestroy'])->name('products.image_destroy');
    Route::post('barcodes/{productId}', [ProductController::class, 'barcodePrint'])->name('barcodeprint');

    Route::resource('productserials', ProductSerialController::class);



    Route::POST('productserialcode', [ProductSerialController::class, 'serialcodePrint'])->name('serialcodeprint');
    Route::post('rangeproductserialcode', [ProductSerialController::class, 'rangeserialcodePrint'])->name('rangeserialcodeprint');




    Route::resource('sub_categorys', SubCategoryController::class);
    Route::resource('categorys', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('productmodels', ProductmodelController::class);
    Route::resource('colors', ColorController::class);
    Route::resource('sizes', SizeController::class);
    Route::resource('units', UnitController::class);

    Route::get('/get-subcategories/{category_id}', [ProductSerialController::class, 'getSubCategories'])->name('get-subcategories');
    Route::get('/get-products/{subcategory_id}', [ProductSerialController::class, 'getProducts'])->name('get-products');

    //Route::get('/',[ProductController::class, 'index'])->name('pindex');;
    Route::get('/search', [ProductController::class, 'search'])->name('search');

    //Route::get('/getProductSerials',[ProductSerialController::class, 'getProductSerials'])->name('getProductSerials');
    Route::get('/getProductSerials/{category_id}/{subcategory_id}/{product_id}/{print_status}', [ProductSerialController::class, 'getProductSerials'])->name('getProductSerials');

    // Md. Masum Work
    Route::get('product_complain_solve_list', [ProductComplainController::class, 'productComplainSolveList'])->name('product_complain_solve_list');
    Route::get('product_complain_list', [ProductComplainController::class, 'productComplainList'])->name('product_complain_list');
    Route::get('product_complain_list_search/{category_id}/{subcategory_id}/{product_id}/{status}/{pdf}', [ProductComplainController::class, 'productComplainListSearch'])->name('product_complain_list_search');
    Route::PUT('complaint_solve/{id}', [ProductComplainController::class, 'complaintSolve'])->name('complaint_solve');
    Route::get('product_feedback_list_search/{category_id}/{subcategory_id}/{product_id}/{pdf}', [ProductFeedbackController::class, 'productFeedbackListSearch'])->name('product_feedback_list_search');
});
