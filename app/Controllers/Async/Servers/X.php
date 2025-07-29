<?php 


use Base\Controllers\ConsoleController;

class X extends ConsoleController
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function boot()
    {
        require_once ROOTPATH . 'routes/async.php';
    }

    public function index()
    {
        $this->boot();
    }

}
/* End of X file */
