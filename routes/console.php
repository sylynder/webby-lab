<?php
defined('COREPATH') or exit('No direct script access allowed');

use Base\Route\Route;
use Base\Console\Route\Command;

/*
| -------------------------------------------------------------------------
| CONSOLE URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific command or controller functions
| that controls cli/console activities. Please make sure your command names don't conflict 
| in all the other route files
|
| $route['route-pattern'] = 'controller/method/segment1/segment2/segment3';
|
| A way to add commands the console.php file comes in this form
| Command::set('route-pattern', 'module/controller/method/segment1/segment2/segment3');
| Command::cli('route-pattern', 'module/controller/method/segment1/segment2/segment3');
|
| Route::cli('route-pattern', 'module/controller/method/segment1/segment2/segment3');
*/

Command::cli('git:books', 'Books/Git');
Command::cli('gist:books', 'Books/GistCommand');
Command::cli('write-something', 'App/WriteCommand');

Command::cli('cc:books', 'BooksCommand::index');
Command::cli('titi:command', 'TitiCommand::index');

Command::cli('csi:command', 'commands/git'); // using command from Commands Folder
Command::cli('book:gist', 'Books/GistCommand'); // using command from Books/Commands Folder

Command::cli('run:reactphp', 'App/Sync::reactphp');

Command::cli('send:emails', 'App/EmailSenderCommand::processQueue');


// Command::set('create-app', 'App/App::index');
// Command::set('create-home', 'App/App::index');

// Command::cli('cli-app','App/App::test');
// Command::cli('digital-ocean','Console/Commands/DoceanCommand');

Command::cli('cli:books', 'Books::index');

Command::cli('my:family', 'Family::index');

Command::cli('swagger:docs', 'Swagger/GenDocs::generate');