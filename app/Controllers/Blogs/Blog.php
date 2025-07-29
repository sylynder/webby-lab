<?php 

use Base\Controllers\WebController;

class Blog extends WebController
{
    public function __construct()
    {
        parent::__construct();
        
        // $this->use->database(); // enable to use database

    }

    public function index()
    {
        // Sample Code Here ...
        echo "Hello World";
    }

}
/* End of Blog file */
