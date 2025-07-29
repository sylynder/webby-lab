<?php

use Base\Route\Route;

Route::get('home-api', ['App', 'index']);

$route = Route::include();