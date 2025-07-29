<?php

namespace App\Middleware;

use Base\Controllers\ApiController;

class ApiMiddleware extends ApiController
{
    public $validate;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->validate = $this->form_validation;
        $this->output->setHeader('Access-Control-Allow-Origin: *');

    }

    protected function middleware() {}

}
