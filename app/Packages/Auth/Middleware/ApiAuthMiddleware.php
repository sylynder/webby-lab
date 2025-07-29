<?php

namespace App\Packages\Auth\Middleware;

use Base\Controllers\ApiController;

class ApiAuthMiddleware extends ApiController
{
    public $validate;

    public function __construct()
    {
        parent::__construct();
        $this->use->library('form_validation');
        $this->validate = $this->form_validation;
        $this->output->setHeader('Access-Control-Allow-Origin: *');
    }
}
