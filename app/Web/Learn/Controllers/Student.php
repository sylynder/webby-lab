<?php 

use Base\Controllers\WebController;

class Student extends WebController
{
    public function __construct()
    {
        parent::__construct();
        
        // $this->use->database(); // enable to use database

    }

    public function index()
    {
        echo "Learn Student Controller";
        // Sample Code Here ...
    }

    public function create()
    {
        // Sample Code Here ...
    }

    public function store()
    {
        // Sample Code Here ...
    }

    public function edit($id)
    {
        $id = clean($id);

        // Sample Code Here ...
    }

    public function update($id)
    {
        $id = clean($id);

        // Sample Code Here ...
    }

    public function delete($id)
    {
        $id = clean($id);
        
        // Sample Code Here ...
    }
}
/* End of Student file */
