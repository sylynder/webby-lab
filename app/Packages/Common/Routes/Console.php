<?php

use Base\Route\Route;

Route::get('home-console', ['App', 'index']);

$route = Route::include();