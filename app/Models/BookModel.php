<?php 

namespace App\Models; 

use Base\Models\EasyModel;

class BookModel extends EasyModel
{
    public $table = ''; // name of table to use
    
    public $primaryKey = ''; // name of primary key field
    
    protected $useSoftDelete = true; // set whether to use soft delete

    public function somefunction($arg1, $arg2) 
    {
    	// Sample code here ...
    }
    
}
/* End of BookModel file */
