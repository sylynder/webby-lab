<?php

/*
| -------------------------------------------------------------------------
| Enable Whoops Error Handler
| -------------------------------------------------------------------------
| Allows easy error handling when building 
| your applications
|
| Set to true to enable this error handler view
*/

$config['whoops_error_handler'] = false;

/*
| -------------------------------------------------------------------------
| Set Editor to use to access error line
| -------------------------------------------------------------------------
| Allows you to go error line in a specified editor
|
| Known editors are sublime, textmate, emacs, macvim, phpstorm,
| idea, vscode, atom, espresso, netbeans 
|
*/

$config['use_editor'] = 'vscode';


/*
| -------------------------------------------------------------------------
| Hide $_ENV variables and $_SERVER data
| -------------------------------------------------------------------------
| Allows you to hide sensitive data
|
*/

$config['hide_sensitive_data'] = true;

/*
| -------------------------------------------------------------------------
| Hide $_SESSION variables
| -------------------------------------------------------------------------
| Allows you to hide session data
|
*/
$config['hide_session_data'] = true;

/*
| -------------------------------------------------------------------------
| Hide $_COOKIE variables
| -------------------------------------------------------------------------
| Allows you to hide cookie data
|
*/
$config['hide_cookie_data'] = true;
