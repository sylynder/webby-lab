<?php

namespace App\Middleware;

use Base\Controllers\WebController;

class EmployerMiddleware extends WebController
{
    public $validate;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->validate = $this->form_validation;
        $this->always();
        // $this->output->enable_profiler(TRUE);

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

    public function checkInitialLogin()
    {
        if (session('user_status') == 'set') {
            route()->to('member/profile/password', session('user_id'))
                ->withError('Please Reset Your Password');
        }
    }

    /**
     * checks if member's session is active
     *
     * @return void
     */
    public function checkActive()
    {
        if (
            !session('client_session') 
            && !session('admin_session') 
            && (session('user_type') != 'employer')
        ) {
            redirect('user/login');
        }

        if (!session('loggedin')) {
            redirect('user/logout');   
        }

        if (session('user_type') != 'employer') {
            redirect('user/logout');
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
            'Ministry/Ministry',
            'Common/Common'
        ]);
    }
}
/* End of CandidateMiddleware file */
