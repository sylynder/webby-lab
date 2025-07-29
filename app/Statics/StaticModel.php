<?php

namespace App\Statics;

use  Base\Statics\ToStatic;

class StaticModel
{
    protected static $instance = 'App/AppModel';

    public static function __callStatic($method, $arguments)
    {
        return ToStatic::make(self::$instance, $method, $arguments);
    }

}
