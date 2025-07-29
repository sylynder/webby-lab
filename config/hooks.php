<?php
defined('COREPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend Webby
| without hacking the core files of CodeIgniter.  
| Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/

if (config_item('whoops_error_handler')) {
    $hook['pre_system'][] = [
        'class'    => 'WhoopsHook',
        'function' => 'bootWhoops',
        'filename' => 'WhoopsHook.php',
        'filepath' => PACKAGEPATH . 'Whoops/Hooks',
        'params'   => []
    ];
}
