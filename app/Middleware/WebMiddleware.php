<?php

namespace App\Middleware;

use Base\Controllers\WebController;

class WebMiddleware extends WebController
{
    public $validate;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->validate = $this->form_validation;
        // $this->output->enable_profiler(TRUE);

    }

    /**
     * Default middleware function 
     * to be used
     *
     * @return void
     */
    public function always() {}

    /**
     * checks if user's session is active
     *
     * @return void
     */
    public function checkActive()
    {
        if (!session('client_session') && session('loggedin') !== true) {
            redirect('');
        }
    }

    /**
     * checks if admin's session is active
     *
     * @return void
     */
    public function checkAdminActive()
    {
        if (!session('admin_session') && session('loggedin') !== true) {
            redirect('admin-panel/login');
        }
    }

    public function userHelper()
    {
        use_helper('Auth.User');
    }

    /**
     * Cache pages
     *
     * @param int $seconds
     * @return void
     */
    public function cachePages($seconds)
    {
        $this->output->cache($seconds);
    }

}
