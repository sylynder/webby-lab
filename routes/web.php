<?php
defined('COREPATH') or exit('No direct script access allowed');

use Base\Route\Route;

/*
| -------------------------------------------------------------------------
| WEB URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
|
| Please make sure route names don't conflict in all the route files
| 
*/

/*
| -------------------------------------------------------------------------
| WEB ROUTING HINT
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions
| that controls WEB activities. Please make sure route names don't conflict 
| in all the other route files
| 
| $route['route-pattern'] = 'module/controller/method/segment1/segment2/segment3';
|
| A new way to add routes also come in this form
| Route::get('route-pattern', 'module/controller/method/segment1/segment2/segment3');
*/

#-------------------------------------------------
#     CUSTOMER ROUTES

Route::get('blog', 'blog/index');

Route::get('documentation', 'app/index')->name('docs');
Route::get('github', 'app/index')->name('repo');
Route::get('download', 'app/index')->name('save.download');

// Route::domain('*.developerkwame.local')->group(function () {});

// // dynamic subdomain routes
// Route::domain('{company}.developerkwame.local')->group(function () {
//     Route::get('domain', 'app/test');
//     Route::get('dynamic', 'app/dynamic');

//     Route::get('test-user', 'backend/dashboard/home');
// });

// Route::domain('learn.developerkwame.local', 'learn')->group(function () {
//     Route::get('domain', 'app/learn');
//     Route::get('dynamic', 'app/dynamic');
//     // Route::get('school', 'school/books');
// });

// // specific routes
// Route::domain('teach.developerkwame.local', 'app/teach')->group(function () {
//     Route::get('domain', 'app/teach');
//     Route::get('dynamic', 'app/dynamic');
// });

// // default sub domain route
// Route::domain('uses.developerkwame.local')->group(function () {
//     Route::get('domain', 'app/uses');
//     Route::get('dynamic', 'app/dynamic');
// });

// Route::get('school', 'school/books');
// Route::get('test-uis', 'Swagger/GenDocs');

// Route::default(function() {


//     Route::get('school', 'school/books');

//     #-------------------------------------------------
//     // dd((new Route)->getHttpHost());
//     Route::get('backend/dashboard', 'backend/dashboard/home');
//     Route::get('customer/in', 'customer/user/home');

//     // #-------------------------------------------------
//     // #     WEBSITE ROUTES
//     Route::get('/login', 'home/login');
//     // Route::get('login', 'home/login', ['as' => 'sss']);
//     // Route::get('blogger', 'home/blog', ['as' => 'open-key']);
//     Route::get('crum', 'home/login')->name('pod');
//     // Route::get('login', 'home/login', ['as' => 'countee']);
//     Route::get('/register', 'home/register', ['as' => 'countev']);

//     Route::get('/your/home', 'customer/home');
//     Route::get('/your/transfer', 'customer/transfer');
//     Route::get('/log_out', 'customer/auth/logout');
// });

// $route['customer'] 				= 'customer/auth/login';
// $route['logout'] 				= 'customer/auth/logout';

// // $route['blog'] 					= 'home/blog';
// // $route['blog/(:any)'] 			= 'home/blog/$1';
// // $route['blog/(:any)/(:any)'] 	= 'home/blog/$1/$2';

// $route['exchange'] 				= 'home/exchange';
// $route['contact'] 				= 'home/contact';
// // $route['about'] 				= 'home/about';
// $route['balances'] 				= 'home/balances';
// $route['deposit'] 				= 'home/deposit';
// $route['deposit/(:any)'] 		= 'home/deposit/$1';
// $route['withdraw'] 				= 'home/withdraw';
// $route['withdraw/(:any)'] 		= 'home/withdraw/$1';
// $route['transfer'] 				= 'home/transfer';
// $route['transactions'] 			= 'home/transactions';
// $route['open_order'] 			= 'home/open_order';
// $route['complete_order'] 		= 'home/complete_order';
// $route['trade_history'] 		= 'home/trade_history';
// $route['register'] 				= 'home/register';
// $route['reset'] 				= 'home/reset';
// $route['profile'] 				= 'home/profile';
// $route['profile_verify'] 		= 'home/profile_verify';

// $route['resetPassword'] 		= 'home/resetPassword';
// $route['forgotPassword'] 		= 'home/forgotPassword';
// $route['paymentform'] 			= 'home/paymentform';
// $route['payout_setting'] 		= 'home/payout_setting';
// $route['(:any)'] 				= 'home/page';
    
// Route::get('books', function() {
//     echo "ee";
// });


// Route::block('domain');
// Route::prefix('learn-with', function() {

//     Route::get('digi', 'home/login')->name('user-login');

// });
