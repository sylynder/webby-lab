<?php

use Base\Route\Route;

// Debug Pages
Route::get('debug', 'Debug::index');
Route::get('debug/log', 'Debug::log');
Route::get('debug/log/(:any)', 'Debug/log/$1');

$route = Route::include();