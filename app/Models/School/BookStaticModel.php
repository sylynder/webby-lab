<?php

namespace App\Models\School;

use  Base\Statics\ToStatic;

class BookStaticModel
{
    protected static $instance = \App\Models\School\BookModel::class;

    public static function __callStatic($method, $arguments)
    {
        return ToStatic::makeStatic(self::$instance, $method, $arguments);
    }

}
