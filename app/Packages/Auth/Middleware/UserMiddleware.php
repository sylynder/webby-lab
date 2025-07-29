<?php

namespace App\Packages\Auth\Middleware;

use Base\Controllers\WebController;

class UserMiddleware extends WebController
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
        $this->checkActive();
        $this->authHelper();
        $this->globalHelper();
    }

    public function checkKycStatus()
    {
        if (session('user_status') == 'set') {
            route()->to('user/kyc-progress')
                ->withError('Please Complete Your KYC');
        }
    }

    public function checkInitialLogin()
    {
        // if (session('user_status') == 'set') {
        //     route()->to('member/profile/password', session('user_id'))
        //         ->withError('Please Reset Your Password');
        // }
    }

    /**
     * checks if member's session is active
     *
     * @return void
     */
    public function checkActive()
    {
        if (!session('client_session') && !session('admin_session')) {
            redirect('user/logout');
        }

        if (!session('loggedin')) {
            redirect('user/logout');   
        }
    }

        /**
     * checks if admin's session is active
     *
     * @return void
     */
    public function checkAdminActive()
    {
        if (!session('admin_session')) {
            route()->to('user/dashboard')->withError('Access Denied');          
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
