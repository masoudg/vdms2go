<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes.
| Simply define the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', ["uses"=>"VoucherController@index"]);

$router->group(['prefix' => 'voucher'], function () use ($router) {
	$router->get('index', ["uses"=>"VoucherController@getAllVouchersList"]);

	$router->get('code/index', ["uses"=>"VoucherCodeController@getAllCodesList"]);

	$router->post('user/index', ["uses"=>"VoucherCodeController@getUserVoucherCodesList"]);

	$router->post('grab', ["uses"=>"VoucherCodeController@grab"]);

	$router->post('redeem', ["uses"=>"VoucherCodeController@redeem"]);
});
