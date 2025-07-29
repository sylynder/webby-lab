Testing New Routing Strategies
content_copy

```php


<?php
defined('CIPATH') OR exit('No direct script access allowed');
 
use Base\CodeIgniter\CodeIgniter;
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
| example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
|
| Examples: my-controller/index -> my_controller/index
|   my-controller/my-method -> my_controller/my_method
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
// Route::$trueHttp = true;
Route::http('home', 'PATCH', 'App/home');
Route::post('cook', 'App/home', ['as' => 'cooker']);
Route::get('admin-panel/login', ['Auth/WebLoginController', 'index']);
 
Route::resources('users', ['module' => 'users', 'controller' => 'cars', 'offset' => 1], false, false);
 
Route::view('books', 'App/index');
 
Route::any('welcome', 'App/index', ['as' => 'sgreetings']);
Route::resources('photos');
Route::uselinks('books', false);
 
Route::prefix('zadmin', function() {
    Route::get('kwame/kwame', 'App/index');
    Route::get('api/caming', 'Admin/App/index/$1');
});
 
Route::prefix('admin', function () {
    Route::get('kwame/kwame', 'App/index');
    Route::get('api/caming', 'Admin/App/index/$1');
});
 
Route::prefix('admin', function() {
    Route::uselinks('users');
});
 
Route::get('jr', 'App/index');
 
Route::prefix('kk/{id}', function () {
    Route::get('kwame', 'App/index');
    Route::get('api/caming', 'Admin/App/index/$1');
});
 
Route::get('api/users/create', 'Something');
Route::partial('users/cars', ['index', 'create', 'show']);
Route::unique(['api/users', '/{id}/{special}'], '/market/$1');
Route::unique(['users', '/{id}/{special}'], '/market', false);
 
Route::prefix('raw', function () {
    Route::get('kwame/kwame', 'App/index');
    Route::get('api/caming', 'Admin/App/index/$1');
});
 
Route::prefix('cars', function () {
    Route::get('parts', 'Cars/engine');
    Route::get('doors', 'Cars/doors'); 
    Route::partial('cars/cars', ['index', 'create', 'show']);
});
 
Route::module('books', function () {
    Route::get('get/books', 'Books/index');
    Route::get('do/books', 'Books/App/index/$1');
});
 
Route::route('articles/(:num)', 'posts->show', function () {
    Route::post('comments', 'comments->show');
    Route::get('category', 'books::list');
});
 
Route::get('signup', 'website/home/signup');
Route::get('login', 'website/home/login');
 
// Route::group('student', function() {
    
//     Route::get('dashboard', 'Website/StudentAccountController::index');
//     Route::get('profile', 'Website/StudentAccountController::profile');
 
//     // Personal Information
//     Route::get('personal-information', 'Website/StudentAccountController::personal');
//     Route::post('save-personal-information', 'Website/StudentAccountController::savePersonalInformation');
//     Route::post('update-personal-information', 'Website/StudentAccountController::updatePersonalInformation');
 
// });

// preceeding forward slash
Route::get('/address-book', ['AppController', 'index'])->name('app.test-app');

Route::get('address-book', ['AppController', 'index'])->name('app.test-app');

Route::get('admin-panel/login', ['Auth/WebLoginController', 'index']);
 
Route::block('jr', 'kk/kwame/kwame');
Route::block('kk');

Route::domain('learn.developerkwame.com')->group(function() {

    Route::prefix('student', function() {
        Route::get('fii', 'home/login')->name('user-login');
    });

    Route::get('yush', 'home/login')->name('bagged');

    Route::prefix('learn', function() {
        Route::get('yush', 'home/login')->name('bagged');
        Route::get('contact', 'home/login')->name('bagged');
    });
   
});

Route::domain('teach.developerkwame.com')->group(function() {

    Route::prefix('student', function() {
        Route::get('fii', 'home/login')->name('user-login');
    });

    Route::get('yush', 'home/login')->name('bagged');
   
});

Route::domain('teach.developerkwame.com')->group(function() {

    Route::prefix('student', function() {
        Route::get('fii', 'home/login')->name('user-login');
    });

    Route::get('yush', 'home/login')->name('bagged');
});

Route::default(function () {

    Route::prefix('student', function() {
        Route::get('fii', 'home/register')->name('user-login');
    });
    
    Route::get('login', 'home/login');
    Route::get('pimp', 'home/register')->name('bagged');

    Route::prefix('learns', function() {
        Route::get('yep', 'home/login')->name('bagged');
        Route::get('contact', 'home/login')->name('bagged');
    });

    Route::prefix('wewe', function() {
        Route::get('yep', 'home/login')->name('bagged');
    });

});


// Route::domain('*.developerkwame.local')->group(function () {});

// dynamic subdomain routes
Route::domain('{company}.developerkwame.local')->group(function () {
    Route::get('domain', 'app/test');
    Route::get('dynamic', 'app/dynamic');

    Route::get('test-user', 'backend/dashboard/home');
});

Route::domain('learn.developerkwame.local', 'learn')->group(function () {
    Route::get('domain', 'app/learn');
    Route::get('dynamic', 'app/dynamic');
    // Route::get('school', 'school/books');
});

// specific routes
Route::domain('teach.developerkwame.local', 'app/teach')->group(function () {
    Route::get('domain', 'app/teach');
    Route::get('dynamic', 'app/dynamic');
});

// default sub domain route
Route::domain('uses.developerkwame.local')->group(function () {
    Route::get('domain', 'app/uses');
    Route::get('dynamic', 'app/dynamic');
});

Route::get('school', 'school/books');

Route::default(function() {

    Route::get('school', 'school/books');

    $route['customer'] 				= 'customer/auth/login';
    $route['log_out'] 				= 'customer/auth/logout';
    #-------------------------------------------------
    // dd((new Route)->getHttpHost());
    Route::get('backend/dashboard', 'backend/dashboard/home');
    Route::get('customer/in', 'customer/user/home');

    // #-------------------------------------------------
    // #     WEBSITE ROUTES
    Route::get('login', 'home/login');
    // Route::get('login', 'home/login', ['as' => 'sss']);
    // Route::get('blogger', 'home/blog', ['as' => 'open-key']);
    Route::get('crum', 'home/login')->name('pod');
    // Route::get('login', 'home/login', ['as' => 'countee']);
    // Route::get('register', 'home/register', ['as' => 'countev']);

    $route['blog'] 					= 'home/blog';
    $route['blog/(:any)'] 			= 'home/blog/$1';
    $route['blog/(:any)/(:any)'] 	= 'home/blog/$1/$2';

    $route['exchange'] 				= 'home/exchange';
    $route['contact'] 				= 'home/contact';
    // $route['about'] 				= 'home/about';
    $route['balances'] 				= 'home/balances';
    $route['deposit'] 				= 'home/deposit';
    $route['deposit/(:any)'] 		= 'home/deposit/$1';
    $route['withdraw'] 				= 'home/withdraw';
    $route['withdraw/(:any)'] 		= 'home/withdraw/$1';
    $route['transfer'] 				= 'home/transfer';
    $route['transactions'] 			= 'home/transactions';
    $route['open_order'] 			= 'home/open_order';
    $route['complete_order'] 		= 'home/complete_order';
    $route['trade_history'] 		= 'home/trade_history';
    $route['register'] 				= 'home/register';
    $route['reset'] 				= 'home/reset';
    $route['profile'] 				= 'home/profile';
    $route['profile_verify'] 		= 'home/profile_verify';

    $route['resetPassword'] 		= 'home/resetPassword';
    $route['forgotPassword'] 		= 'home/forgotPassword';
    $route['paymentform'] 			= 'home/paymentform';
    $route['payout_setting'] 		= 'home/payout_setting';
    // $route['(:any)'] 				= 'home/page';
    
});

Route::block('domain');

```

#### Implementing Subdomain

https://github.com/mrhbozkurt/codeigniter-3-subdomain/blob/main/hooks/HostNameRouter.php

https://github.com/Patroklo/codeigniter-static-laravel-routes/blob/master/application/libraries/Route.php

https://github.com/Thedijje/CI_helper/blob/master/application/models/Helper_model.php

https://github.com/nguyenanhung/codeigniter-framework/blob/master/helpers/common.php




```php

// Normal app/system paths
// dd(SUBDOMAIN);

// dd();

Route::domain('learn')->module('student', function () {
    Route::get('fii', 'Learn/LearnController::index');
});



<?php
use Base\Route\Route;

function whichSubRoute()
{
    $domains = [
        "learn" => "learn/",
        "test" => "test/"
    ];

    // dd(STDIN);
    $splitDomain = !(PHP_SAPI === 'cli' or defined('STDIN'))
        ? $_SERVER['HTTP_HOST']
        : '';

    [$currentDomain] = explode('.', $splitDomain);
    // dd($curr, array_key_exists($curr[0], $subs), array($curr[0], $subs[$curr[0]]));
    if (array_key_exists($currentDomain, $domains)) {
        return array($currentDomain, $domains[$currentDomain]);
    }

    return false;
}

//due to the the way this setup works, some controller references
//can be found multiple times (and in no particular order).
//also note due to this setup, each method has its own default and 404
[$subdomain, $uriDomain] = whichSubRoute();
// dd($route);
if ($subdomain !== false) {

    if ($subdomain == "learn") {

        $route['default_controller'] = "learn";
        $route['learn/(:any)'] = $uriDomain . "student";
        $route['learn/(:any)']                   = $uriDomain . 'm_welcome';
        dd($uriDomain);
        // Route::context('learn');
        $route['404_override'] = '';
       
        
        // dd($route, $uriDomain);
        //controllers outside of "/api"
    }
    if ($subdomain == "m") {
        
        $route['default_controller'] = "welcome";
        $route['404_override'] = '';

        //start version 1 (mobile)
        $route['welcome']                   = $uriDomain . 'm_welcome';
        $route['dashboard']                 = $uriDomain . 'm_dashboard';
        $route['user/(:any)']               = $uriDomain . 'm_userinfo/index/$1';

        // $route['reg']                       =
        
        //controllers outside of "/m"
        $route['login/auth']                = 'login/auth';
        $route['logout/mobile']             = 'logout/mobile';

        //end version 1 (mobile)
    }
} else {
    $route['default_controller'] = "App/Test";
    $route['404_override'] = '';
}

// dd($route);

Route::partial('users/cars', ['index', 'create', 'show']);
Route::context('learn');
// Route::context('learn');

```