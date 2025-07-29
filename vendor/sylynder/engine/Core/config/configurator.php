<?php
defined('COREPATH') or exit('No direct script access allowed');

use Base\Helpers\Configurator;

/*
|--------------------------------------------------------------------------
| Get all Configuration files
|--------------------------------------------------------------------------
| 
| Load all configuration files here
|
*/

// $files = glob(ROOTPATH . "config" . DIRECTORY_SEPARATOR . "*.php"); 

// // Exclude these specified files
// $exclude = [
//     'autoload',
//     'constants',
//     'database',
//     'hooks',
//     'profiler',
//     'commands',
// ];

// foreach ($files as $file) 
// {
//     foreach ($exclude as $name) 
//     {
//         if (stripos($file, $name) !== false) {
//             continue 2; // break out of 2 levels of loops 
//         }
//     }

//     require_once $file;
// }

function getConfigFiles() {
    $files = glob(ROOTPATH . "config" . DIRECTORY_SEPARATOR . "*.php");
    $exclude = ['autoload', 'constants', 'database', 'hooks', 'profiler', 'commands'];
    
    foreach ($files as $file) {
        $skip = false;
        foreach ($exclude as $name) {
            if (stripos($file, $name) !== false) {
                $skip = true;
                break;
            }
        }
        if (!$skip) yield $file;
    }
}

static $configsLoaded = false;

if (!$configsLoaded) {
    foreach (getConfigFiles() as $file) {
        $tempConfig = [];
        require_once $file;
        $config = isset($config) ? array_merge($config, $tempConfig) : $tempConfig;
        unset($tempConfig); // ← IMMEDIATE CLEANUP!
    }
    $configsLoaded = true;
}

// Load all configs with caching enabled
// Configurator::loadAllConfigs(true); // true = use caching
