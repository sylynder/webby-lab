<?php 

use Base\Controllers\WebController;

class Books extends WebController
{
    public function __construct()
    {
        parent::__construct();
        
        // $this->use->database(); // enable to use database

    }

    public function index()
    {
       echo 'hello books';
    }

    public function create()
    {
        echo 'hello create books';
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
/* End of BookController file */
