<?php 

namespace App\Models\School; 

use Base\Models\EasyModel;

class BookModel extends EasyModel
{

    public $table = 'books'; // name of table to use
    
    public $primaryKey = 'id'; // name of primary key field
    
    protected $useSoftDelete = false; // set whether to use soft delete

    public function somefunction($arg1, $arg2) 
    {
    	return [$arg1, $arg2];
    }
    
}
/* End of BookModel file */
