<?php

use Base\Route\Route;

Route::any('process:mail', 'App/index');

// Route::module('queue', function() {
    Route::any('process:mail', '');
    // Route::any('process:sms', '');
    Route::any('process:job', '');
    Route::any('process:simple-mail', '');
    Route::any('process:push-notification', '');
    Route::any('process:failed-job', '');
    Route::any('process:clear-jobs', '');
// });

$route = Route::include();
