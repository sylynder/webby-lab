<?php 

namespace App\Models; 

use Base\Json\Db;
use Base\Json\Interfaces\Model;

class WarModel extends Db implements Model
{
    public $file = 'students'; // a json file name
    public $database = 'school'; // a folder to store json file

    public function __construct()
    {
        parent::__construct($this->database);
        $this->useTable();
    }

    public function useTable()
    {
        $this->from($this->file);
    }

}
/* End of WarModel file */
