<?php 

namespace App\Middleware;

use Base\Controllers\WebController;

class AppMiddleware extends WebController
{
    public $validate;

    public function __construct()
    {
        parent::__construct();
        $this->validate = use_form_validation();
        $this->use->database();
        // $this->use->library('session');
    }

    /**
     * Default middleware function 
     * to be used
     *
     * @return void
     */
    public function always() {}

}
/* End of AppMiddleware file */
