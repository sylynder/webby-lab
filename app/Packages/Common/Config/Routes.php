<?php

use Base\Route\Route;

Route::get('home-cute', 'App::index');

$route = Route::include();

// customer/auth/login