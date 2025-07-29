<?php

use Base\Helpers\Uuid;
use Base\Http\HttpStatus;

class AuthService extends \Base_Service
{
    private $ci;
    
    public function __construct() {
        $this->load->library('Auth/AuthToken');
        $this->load->model('Auth/UserModel', 'user');
    }

}
