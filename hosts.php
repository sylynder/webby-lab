<?php

$hosts['main'] = 'developerkwame.local';
$hosts['learn'] = 'learn.developerkwame.local';
$hosts['learn-with'] = 'learn-with.developerkwame.local';


/*
	Define the SITE constant.
*/

// $c = [];
foreach ($hosts as $site => $host) {

    // $c[] = $host;

	// if ($_SERVER['HTTP_HOST'] === $host)
	// {
	// 	define('SITE', $site);
    //     // $_ENV['HOST_NAME'] = $site;
    //     // $_SERVER['HOST_NAME'] = $site;
	// 	break;
	// }

}

$config['hosts'] = $hosts;//$_ENV['HOST_NAME'] ?? '';

// dd($c, $_SERVER['SERVER_NAME']);

// sudo ./ssl-serve webby.local 8085 2020