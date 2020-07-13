<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// test zone
Route::get('testmail', 'OrdersController@testSendMail');
Route::get('testsms', 'OrdersController@sendSms');
//

Route::get("hash-password/{password}", 'UsersController@hashPassword');
Route::get('translate/{keyword}', 'ProductsController@translate');

Route::post('admin/login', 'UserAdminsController@login');
Route::post('admin/logout', 'UserAdminsController@logout');


// Route::group([/*'prefix' => 'admin',*/'middleware' => ['assign.guard:admin_users','jwt.auth']],function (){

	Route::post('admin/order/list/pending', 'OrdersController@pendingList');
	Route::post('admin/importer/list/pending', 'ImportersController@pendingList');
	Route::post('admin/topup/list/pending', 'MoneyBagsController@topupPendingList');
	Route::post('admin/transfer/list/pending', 'MoneyBagsController@transferPendingList');
	Route::post('admin/deposit/list/pending', 'MoneyBagsController@depositPendingList');

	Route::post('admin/monitor/pending', 'UserAdminsController@getMonitorData');
	
	Route::post('admin/order/list', 'OrdersController@list');
	Route::post('admin/order/get', 'OrdersController@get');
	Route::post('admin/order/update', 'OrdersController@updateOrder');
	Route::post('admin/order/status/update', 'OrdersController@updateOrderStatus');
	Route::post('admin/order/upload', 'OrdersController@uploadExcel');
	Route::post('admin/order/cancel', 'OrdersController@cancelOrder');
	Route::post('admin/order/sms/transport-payment', 'OrdersController@sendTransportPaymentSMS');
	Route::post('admin/order/tracking/delete', 'OrdersController@deleteTrack');
	Route::post('admin/order/cancel/cancel-status', 'OrdersController@cancelCancelStatus');
	
	Route::post('admin/topup/list', 'MoneyBagsController@getRequestTopupList');
	Route::post('admin/topup/status/update', 'MoneyBagsController@updateTopupStatus');

	Route::post('admin/pay/list', 'MoneyBagsController@getPayList');

	Route::post('admin/importer/list', 'ImportersController@list');
	Route::post('admin/importer/group', 'ImportersController@listGroup');
	Route::post('admin/importer/status/update', 'ImportersController@updateImporterStatus');
	Route::post('admin/importer/upload', 'ImportersController@uploadExcel');
	Route::post('admin/importer/delete', 'ImportersController@delete');
	
	Route::post('admin/user/list', 'UsersController@getUserList');
	Route::post('admin/user/address', 'UsersController@getUserAddress');
	
	Route::post('admin/customer/list', 'UsersController@getCustomerList');
	Route::post('admin/customer/withdrawn', 'MoneyBagsController@withdrawnMoney');
	Route::post('admin/customer/refund', 'MoneyBagsController@refundMoney');
	Route::post('admin/customer/update/level', 'UsersController@updateUserLevel');

	Route::post('admin/exchange-rate/list', 'ExchangeRatesController@getExchangeRateList');
	Route::post('admin/exchange-rate/update', 'ExchangeRatesController@updateExchangeRate');

	Route::post('admin/exchange-rate-transfer/list', 'ExchangeRatesController@getExchangeRateTransferList');
	Route::post('admin/exchange-rate-transfer/update', 'ExchangeRatesController@updateExchangeRateTransfer');

	Route::post('admin/manage-admin/list', 'UserAdminsController@list');
	Route::post('admin/manage-admin/get', 'UserAdminsController@get');
	Route::post('admin/manage-admin/update', 'UserAdminsController@update');

	Route::post('admin/transfer/list', 'MoneyBagsController@getTransferList');
	Route::post('admin/transfer/status/update', 'MoneyBagsController@updateTransferStatus');

	Route::post('admin/deposit/list', 'MoneyBagsController@getDepositList');
	Route::post('admin/deposit/status/update', 'MoneyBagsController@updateDepositStatus');

	Route::post('admin/transport-rate/list', 'TransportRatesController@list');
	Route::post('admin/transport-rate/get', 'TransportRatesController@get');
	Route::post('admin/transport-rate/update', 'TransportRatesController@update');
	Route::post('admin/transport-rate/update/rate-level', 'TransportRatesController@updateRateLevel');

	Route::post('admin/landing-page/list', 'LandingPageController@list');
	Route::post('admin/landing-page/get', 'LandingPageController@get');
	Route::post('admin/landing-page/update', 'LandingPageController@update');

	Route::post('admin/history/refund', 'MoneyBagsController@getRefundList');
	Route::post('admin/history/withdrawn', 'MoneyBagsController@getWithdrawnList');	
// });

Route::post('login', 'UsersController@login');
Route::post('register', 'UsersController@register');
Route::post('forgot-pass/request', 'UsersController@forgotPassRequest');
Route::post('forgot-pass/check', 'UsersController@forgotPassCheck');
Route::post('forgot-pass/update', 'UsersController@forgotPassUpdate');

Route::post('landing-page/show', 'LandingPageController@show');
Route::post('exchange-rate/get', 'ExchangeRatesController@getCurrentExchangeRate');
Route::post('transport-rate/show', 'TransportRatesController@show');
Route::post('logout', 'UsersController@logout');

Route::post('user/money-bag/balance', 'MoneyBagsController@getAccountBalance');
Route::post('item/get', 'ProductsController@getItem');

Route::post('user/update', 'UsersController@updateData');
Route::post('address/remove', 'UsersController@removeAddress');
Route::post('order/confirm', 'OrdersController@confirmOrder');

Route::post('topup/inform', 'MoneyBagsController@topup');
Route::post('topup/inform/test', 'MoneyBagsController@topupTest');
Route::post('pay/inform', 'MoneyBagsController@pay');
Route::post('pay/history', 'MoneyBagsController@payHistory');

Route::post('order/list/by-user', 'OrdersController@listByUser');
Route::post('order/list/by-user-status', 'OrdersController@listByUserAndStatus');
Route::post('order/transport-company/update', 'OrdersController@updateTransportCompany');

Route::post('importer/list/by-user', 'ImportersController@listByUser');
Route::post('importer/get', 'ImportersController@get');
Route::post('importer/update', 'ImportersController@update');

Route::post('cart/get', 'ProductsController@getCartSession');
Route::post('cart/update', 'ProductsController@updateCartSession');

Route::group(['middleware' => ['before' => 'jwt.auth']], function() {

	
	Route::post('test-auth', 'UsersController@testAuth');
	

});




