
<?php

use Base\Models\EasyModel;

class FrogModel extends EasyModel
{
    public $notification = '';
    public $config = '';

    public function __construct()
    {
        $this->notification = config('frog')['notification_table'];
        $this->config = config('frog')['config_table'];
    }

    public function table($table = '') {
        
        if (empty($table)) {
            $table = $this->notification;
        }

        $this->table = $table;

        return $this;
    }
}
