<?php

namespace App\Packages\Debug\Middleware;

use Base\Controllers\WebController;

class DebugMiddleware extends WebController
{
    public $validate;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->validate = $this->form_validation;
        $this->checkAdminActive();
    }

    /**
     * Default middleware function 
     * to be used
     *
     * @return void
     */
    public function always()
    {   
        $this->checkDebugAccess();
    }

    private function checkDebugAccess()
    {
        if (!can('manage', 'debug')) {
            route()->to('admin.dashboard')->withError('Can not view debug logs');
        }
    }

    /**
     * checks if admin's session is active
     *
     * @return void
     */
    public function checkAdminActive()
    {
        if (!session('active') && session('loggedin') !== true) {
            redirect('admin-panel/login');
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
}
