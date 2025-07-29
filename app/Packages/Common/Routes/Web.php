<?php

use Base\Route\Route;

Route::get('home-web', ['App', 'index']);

$route = Route::include();