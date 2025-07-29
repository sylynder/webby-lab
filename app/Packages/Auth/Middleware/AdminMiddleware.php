<?php

namespace App\Packages\Auth\Middleware;

use Base\Controllers\WebController;

class AdminMiddleware extends WebController
{
    public $validate;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->validate = $this->form_validation;
        $this->checkAdminActive();
        $this->authHelper();
        $this->globalHelper();

    }

    /**
     * Default middleware function 
     * to be used
     *
     * @return void
     */
    public function always(){}

    /**
     * checks if admin's session is active
     *
     * @return void
     */
    public function checkAdminActive()
    {
        if (!session('admin_session')) {
            route()->to('admin/logout')->withError('Access Denied');      
        }

        if (!session('loggedin')) {
            route()->to('admin/logout')->withError('Access Denied');         
        }
        
    }

    public function authHelper()
    {
        use_helper([
            'Auth.Util',
            'Auth.User',
            'Auth.Auth',
            'Auth.Roles',
            'Auth.Permissions'
        ]);
    }

    public function globalHelper(){}
}
