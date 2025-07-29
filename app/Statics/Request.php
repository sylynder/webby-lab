<?php

namespace App\Statics;

use Base\Statics\ToStatic;
use CI_Input;

/**
 * @method get()
 */
class Request extends ToStatic
{
    public static function getFullyQualifiedClass()
    {
        return CI_Input::class;
    }
  
}