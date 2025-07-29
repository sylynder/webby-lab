<?php

use Base\Controllers\WebController;

class WebLogoutController extends WebController
{
    // User Logout View
    // private $logoutView = 'website.home';
    private $logoutView = 'auth.user-login';

    // User Logout Route
    private $logoutRoute = '';

    // Admin Logout View
    private $adminLogoutView = 'admin.auth.login';

    // User Logout Route
    private $adminLogoutRoute = '';

    private $defaultIndex = 'user';

    private $authConfig;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('Authy', null, 'auth');
        $this->load->service('UserService', null, 'user');
    }

    public function index()
    {
        $this->{$this->defaultIndex}();
    }

    public function user($url = '')
    {
        $this->auth->logout();

        success_message("You've been logged out successfully");

        if ($url) {
            redirect($url);
            exit;
        }

        if ($this->logoutRoute) {
            redirect($this->logoutRoute);
            exit;
        }
        
        $this->setTitle('Home');
        return layout('users.layouts.auth', $this->logoutView, $this->data);
        // return view($this->logoutView, $this->data);
    }

    public function admin($url = null)
    {

        $this->auth->logout();

        success_message("You've been logged out successfully");

        if ($url) {
            redirect($url);
        }

        if ($this->adminLogoutRoute) {
            redirect($this->adminLogoutRoute);
            exit;
        }

        $this->setTitle('Admin Login');
        return view($this->adminLogoutView, $this->data);
    }
}
