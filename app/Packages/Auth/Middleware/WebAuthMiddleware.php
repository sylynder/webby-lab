<?php

namespace App\Packages\Auth\Middleware;

use Base\Controllers\WebController;

class WebAuthMiddleware extends WebController
{
    public $validate;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->validate = $this->form_validation;
        $this->always();
    }

    /**
     * Default middleware function 
     * to be used
     *
     * @return void
     */
    public function always()
    {
        $this->authHelper();
        $this->globalHelper();
    }

    public function checkUserInitialLogin(
        $routeT0 = 'user/set-account', 
        $message = 'Please Setup Your Account'
    ) {
        if (session('user_status') == 'set') {
            route()->to($routeT0, session('user_id'))
                ->withError($message);
        }
    }

    /**
     * Checks if user's session is active
     *
     * @return void
     */
    public function checkUserActive($redirectTo = 'user/logout')
    {
        if (!session('client_session') && !session('admin_session')) {
            redirect($redirectTo);
        }

        if (!session('loggedin')) {
            redirect($redirectTo);
        }
    }

    /**
     * Checks if superadmin's, admin's, staff sessions are active
     *
     * @return void
     */
    public function checkAdminActive(
        $redirectTo = "admin/dashboard", 
        $redirectLogout = "admin/logout"
    ) {
        if (!session('admin_session') ) {
            route()->to($redirectTo)->withError('Access Denied');          
        }

        if (!session('loggedin')) {
            route()->to($redirectLogout)->withError('Access Denied');         
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

    public function globalHelper()
    {
        use_helper([
            // include all needed helpers,
        ]);
    }
}
