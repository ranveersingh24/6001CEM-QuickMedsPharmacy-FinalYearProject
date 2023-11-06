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




Auth::routes();


Route::post('Contact','HomeController@save');



Route::get('/', 'HomeController@index')->name('home');
Route::get('/Listing', 'HomeController@listing')->name('listing');
Route::get('/Mall', 'HomeController@mall')->name('mall');
Route::get('/Details/{name}/{id}', 'HomeController@details')->name('details');

Route::post('/getVerifyCode', 'AjaxController@getVerifyCode')->name('getVerifyCode');
Route::post('/resetVerifyCode', 'AjaxController@resetVerifyCode')->name('resetVerifyCode');
Route::post('/CheckLogin', 'AjaxController@CheckLogin')->name('CheckLogin');

Route::get('/auth/redirect/{provider}', 'GoogleSocialController@redirect');
Route::get('/callback/{provider}', 'GoogleSocialController@callback');
Route::get('/About', 'HomeController@about')->name('about');
Route::get('/faqs', 'HomeController@faqs')->name('faqs');
Route::get('/Contact', 'HomeController@Contact')->name('Contact');
Route::get('/VerifyAccount/{user_id}', 'HomeController@VerifyAccount')->name('VerifyAccount');
Route::get('/verify_success', 'HomeController@verify_success')->name('verify_success');
Route::get('/product_anti_counterfeiting', 'HomeController@product_anti_counterfeiting')->name('product_anti_counterfeiting');

Route::group(['middleware' => 'auth:web,merchant,admin'], function () {

	
	Route::get('/Profile', 'HomeController@profile')->name('profile');
	Route::get('/MyOrder', 'HomeController@order_list')->name('order_list');
	Route::get('/MyWishList', 'HomeController@wish_list')->name('wish_list');
	Route::get('/OrderDetails/{no}', 'HomeController@order_detail')->name('order_detail');
	Route::get('/ChangePassword', 'HomeController@changePassword')->name('changePassword');
	

	Route::get('/BankAccount', 'HomeController@bank_account')->name('bank_account');
	Route::get('/BankAccount/{id}', 'HomeController@bank_account_edit')->name('bank_account_edit');
	Route::get('/PendingShipping', 'HomeController@pending_shipping')->name('pending_shipping');
	Route::get('/PendingReceive', 'HomeController@pending_receive')->name('pending_receive');
	Route::get('/CompletedOrder', 'HomeController@completed_order')->name('completed_order');
	Route::get('/CancelledOrder', 'HomeController@cancelled_order')->name('cancelled_order');

	Route::post('/BankAccount', 'HomeController@bank_account_save')->name('bank_account_save');

	
	Route::post('/Checkout', 'HomeController@postCheckout')->name('checkout');
	Route::post('/Profile', 'HomeController@updateProfile')->name('profile');
	Route::post('/updatePassword', 'HomeController@updatePassword')->name('updatePassword');
	Route::resource('AddressBook', 'UserShippingAddressController', ['as'=> 'AddressBook']);

	//Frontend
	
	Route::post('/SelectCart', 'AjaxController@SelectCart')->name('SelectCart');
	Route::post('/changeDefaultAddress', 'AjaxController@changeDefaultAddress')->name('changeDefaultAddress');
	Route::post('/deleteAddress', 'AjaxController@deleteAddress')->name('deleteAddress');
	Route::post('/Favourite', 'AjaxController@add_wish')->name('Favourite');
	Route::post('/add_to_wish', 'AjaxController@add_to_wish')->name('add_to_wish');
	Route::post('/remove_wish', 'AjaxController@remove_wish')->name('remove_wish');
	Route::post('/Repayment', 'AjaxController@Repayment')->name('Repayment');
	Route::post('/setBankDefault', 'AjaxController@setBankDefault')->name('setBankDefault');

	Route::get('/MySetting', 'HomeController@my_setting')->name('my_setting');
	Route::post('/add_new_address', 'HomeController@add_new_address')->name('add_new_address');
	Route::get('/Checkout', 'HomeController@checkout')->name('checkout');

	Route::post('/update_complete', 'AjaxController@update_complete')->name('update_complete');
});
Route::get('/CountCart', 'AjaxController@CountCart')->name('CountCart');
Route::post('/AddToCart', 'AjaxController@AddToCart')->name('AddToCart');
Route::get('/setNewGuest', 'AjaxController@setNewGuest')->name('setNewGuest');
Route::post('/placeOrder', 'HomeController@placeOrder')->name('placeOrder');
Route::post('/getVariation', 'AjaxController@getVariation')->name('getVariation');
Route::post('/getVariationPoint', 'AjaxController@getVariationPoint')->name('getVariationPoint');
Route::post('/deleteCart', 'AjaxController@deleteCart')->name('deleteCart');
Route::post('/updateQuantity', 'AjaxController@updateQuantity')->name('updateQuantity');
Route::get('/PaymentProcess/{transactions}', 'HomeController@PaymentProcess')->name('PaymentProcess');
Route::get('/Payment_Error/', 'HomeController@Payment_Error')->name('Payment_Error');
Route::post('/guestAgent/', 'AjaxController@guestAgent')->name('guestAgent');


Route::get('/SelectHeaderCart', 'AjaxController@SelectHeaderCart')->name('SelectHeaderCart');


Route::get('/admin_login', 'Auth\AdminLoginController@ShowAdminLogin')->name('admin_login');

Route::post('/admin_login', 'Auth\AdminLoginController@login')->name('admin_login');
Route::post('/admin_logout', 'Auth\AdminLoginController@admin_logout')->name('admin_logout');

Route::group(['middleware' => 'auth:admin,merchant,web'], function () {
	
	Route::get('products/{id}/stock', 'Backend\ProductController@stock')->name('stock');
	Route::post('products/{id}/stock', 'Backend\ProductController@Submitstock')->name('stock');

	Route::get('products/packages/add', 'Backend\ProductController@packages_add')->name('packages_add');
	Route::post('products/packages/add', 'Backend\ProductController@packages_add_save')->name('packages_add_save');

	Route::get('products/packages/{id}/edit', 'Backend\ProductController@packages_edit')->name('packages_edit');
	Route::post('products/packages/{id}/edit', 'Backend\ProductController@packages_edit_save')->name('packages_edit_save');

	Route::get('products/packages_list/', 'Backend\ProductController@packages_list')->name('packages_list');

	Route::resource('dashboards', 'Backend\DashboardController', ['as'=> 'dashboard']);
	Route::resource('admins', 'Backend\AdminController', ['as'=> 'admin']);

	Route::resource('products', 'Backend\ProductController', ['as'=> 'product']);


	Route::resource('categories', 'Backend\CategoryController', ['as'=> 'category']);
	Route::resource('brands', 'Backend\BrandController', ['as'=> 'brand']);
	Route::resource('promotions', 'Backend\PromotionController', ['as'=> 'promotion']);
	Route::resource('transactions', 'Backend\TransactionController', ['as'=> 'transaction']);
	Route::resource('sub_categories', 'Backend\SubCategoryController', ['as'=> 'sub_category']);
	Route::resource('user_permissions', 'Backend\UserPermissionController', ['as'=> 'user_permission']);
	Route::resource('members', 'Backend\MemberController', ['as'=> 'member']);

	Route::get('products/{id}/variation_stock', 'Backend\ProductController@variation_stock')->name('variation_stock');
	Route::post('products/{id}/variation_stock', 'Backend\ProductController@SubmitVariationStock')->name('variation_stock');
	

	Route::post('getProducts', 'Backend\AjaxController@getProducts')->name('getProducts');
	Route::post('/actionProduct/', 'Backend\AjaxController@actionProduct')->name('actionProduct');
	Route::post('sortingPackagesProduct/', 'Backend\AjaxController@sortingPackagesProduct')->name('sortingPackagesProduct');

	Route::post('add_permission_level', 'Backend\UserPermissionController@add_permission_level')->name('add_permission_level');


	Route::get('setting_shipping_fee', 'Backend\SettingController@setting_shipping_fee')->name('setting_shipping_fee');
	Route::post('save_setting_shipping_fee', 'Backend\SettingController@save_setting_shipping_fee')->name('save_setting_shipping_fee');

	Route::get('setting_customer_feedback', 'Backend\SettingController@setting_customer_feedback')->name('setting_customer_feedback');
    Route::get('setting_customer_feedback', 'Backend\SettingController@index')->name('setting_customer_feedback');
	

	Route::get('setting_website_images', 'Backend\SettingController@setting_website_images')->name('setting_website_images');
	Route::post('save_setting_website_images', 'Backend\SettingController@save_setting_website_images')->name('save_setting_website_images');

	
	Route::post('saveNewPassword/{id}', 'Backend\MerchantController@saveNewPassword')->name('saveNewPassword');
	Route::post('saveMemberNewPassword/{id}', 'Backend\MemberController@saveMemberNewPassword')->name('saveMemberNewPassword');

	Route::post('/uploadBankSlip', 'Backend\TransactionController@uploadBankSlip')->name('uploadBankSlip');

	
	Route::get('sales_report', 'Backend\ReportController@sales_report')->name('sales_report');
	Route::get('order_report', 'Backend\ReportController@order_report')->name('order_report');


	Route::post('/getBankDetails', 'AjaxController@getBankDetails')->name('getBankDetails');

	Route::get('setting_uom', 'Backend\SettingController@setting_uom')->name('setting_uom');
	Route::post('setting_uom_save', 'Backend\SettingController@setting_uom_save')->name('setting_uom_save');

	Route::post('CKEditorUploadImage', 'Backend\AjaxController@CKEditorUploadImage')->name('CKEditorUploadImage');

	Route::get('transaction_invoice/{transaction_no}', 'Backend\TransactionController@transaction_invoice')->name('transaction_invoice');

	Route::get('setting_banner', 'Backend\SettingController@setting_banner')->name('setting_banner');

	Route::get('print_security_code/{id}/{vid}', 'Backend\ProductController@print_security_code')->name('print_security_code');

	
});


//Ajax
//Backend
Route::post('/uploadImage/{id}', 'Backend\AjaxController@uploadImage')->name('uploadImage');
Route::get('/LoadImage/{id}', 'Backend\AjaxController@LoadImage')->name('LoadImage');
Route::get('/DeleteImage/{id}', 'Backend\AjaxController@DeleteImage')->name('DeleteImage');

Route::post('/uploadCategoryImage/{id}', 'Backend\AjaxController@uploadCategoryImage')->name('uploadCategoryImage');
Route::get('/LoadCategoryImage/{id}', 'Backend\AjaxController@LoadCategoryImage')->name('LoadCategoryImage');
Route::get('/DeleteCategoryImage/{id}', 'Backend\AjaxController@DeleteCategoryImage')->name('DeleteCategoryImage');



Route::post('/SetPermission/', 'Backend\AjaxController@SetPermission')->name('SetPermission');
Route::post('/UnsetPermission/', 'Backend\AjaxController@UnsetPermission')->name('UnsetPermission');
Route::get('/GetPermission/', 'Backend\AjaxController@GetPermission')->name('GetPermission');
Route::post('/change_transaction_action/', 'Backend\AjaxController@change_transaction_action')->name('change_transaction_action');
Route::post('/change_withdrawal_transaction_action/', 'Backend\AjaxController@change_withdrawal_transaction_action')->name('change_withdrawal_transaction_action');

Route::post('/getItemCode', 'Backend\AjaxController@getItemCode')->name('getItemCode');

Route::post('/UserStatus', 'Backend\AjaxController@UserStatus')->name('UserStatus');
Route::post('/ProductStatus', 'Backend\AjaxController@ProductStatus')->name('ProductStatus');
Route::post('/CategoryStatus', 'Backend\AjaxController@CategoryStatus')->name('CategoryStatus');
Route::post('/BrandStatus', 'Backend\AjaxController@BrandStatus')->name('BrandStatus');
Route::post('/PromotionStatus', 'Backend\AjaxController@PromotionStatus')->name('PromotionStatus');
Route::post('/setFeatured', 'Backend\AjaxController@setFeatured')->name('setFeatured');


Route::post('/uploadBannerImage/', 'Backend\AjaxController@uploadBannerImage')->name('uploadBannerImage');
Route::get('/LoadBannerImage', 'Backend\AjaxController@LoadBannerImage')->name('LoadBannerImage');
Route::get('/DeleteBannerImage/{id}', 'Backend\AjaxController@DeleteBannerImage')->name('DeleteBannerImage');

Route::post('/InsertQrList', 'Backend\AjaxController@InsertQrList')->name('InsertQrList');
Route::post('/variation_list', 'Backend\AjaxController@variation_list')->name('variation_list');


Route::post('/SubCategoryStatus', 'Backend\AjaxController@SubCategoryStatus')->name('SubCategoryStatus');
Route::post('/GetSubCategory', 'Backend\AjaxController@GetSubCategory')->name('GetSubCategory');
Route::post('/getSubItemCode', 'Backend\AjaxController@getSubItemCode')->name('getSubItemCode');

