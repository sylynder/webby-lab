<?php

$config['authy_db'] = [
    'dsn' => '',
    'hostname' => APP_DB_HOSTNAME,
    'username' => APP_DB_USERNAME,
    'password' => APP_DB_PASSWORD,
    'database' => APP_AUTH_DB,
    'dbdriver' => defined('APP_DB_DRIVER') ? APP_DB_DRIVER : 'mysqli',
    'dbprefix' => '',
    'pconnect' => false,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => false,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_general_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => false,
    'failover' => [],
    'save_queries' => true,
];

$db['authy_db'] = $config['authy_db'];
