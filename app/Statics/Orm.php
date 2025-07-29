<?php

namespace App\Statics;

use Base\Models\EasyModel;
use Base\Statics\ToStatic;

class Orm
{
    protected static $instance = EasyModel::class;

    public static function use($model = 'App/AppModel')
    {
        return self::$instance = $model;
    } 

    public static function __callStatic($method, $arguments)
    {
        return ToStatic::make(self::$instance, $method, $arguments);
    }

}
