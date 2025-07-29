<?php

namespace App\Statics;

use  Base\Statics\ToStatic;

class Model
{
    protected static $instance = 'App/AppModel';

    public static function use(string $model = 'App/AppModel')
    {
        return self::$instance = $model;
    } 

    public static function __callStatic($method, $arguments)
    {
        return ToStatic::make(self::$instance, $method, $arguments);
    }

}
