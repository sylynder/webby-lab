<?php 

use Base\Controllers\ConsoleController;

class Git extends ConsoleController
{
    public function __construct()
    {
        parent::__construct();

        // use this for consoles made to work 
        // in only development environments
        // $this->onlydev();
    }

    public function index()
    {
        // Sample Code Here ...
        echo $this->success('Git Commands Console');
    }

}
/* End of Git file */
