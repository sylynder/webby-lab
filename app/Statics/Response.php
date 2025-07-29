<?php

namespace App\Statics;

use Base\Statics\ToStatic;
use App\Classes\MyClass;


/**
 * @method get()
 */
class Response extends ToStatic
{
    public static function getFullyQualifiedClass()
    {
        return MyClass::class;
        // return app('input');
    }
  
}