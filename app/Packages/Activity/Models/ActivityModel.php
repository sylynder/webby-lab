<?php

use Base\Models\EasyModel;

class ActivityModel extends EasyModel
{
    public $table = 'activity_logs';
    public $primaryKey = 'id';
    public $useSoftDelete = false;
    
}
