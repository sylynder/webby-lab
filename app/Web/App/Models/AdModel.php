<?php 

use Base\Models\EasyModel;

class AdModel extends EasyModel
{
    public $table = 'advertisement'; // name of table to use
    
    public $primaryKey = 'id'; // name of primary key field
    
    protected $useSoftDelete = false; // set whether to use soft delete

    
}
/* End of AppModel file */
