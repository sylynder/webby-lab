<?php

use App\Enums\Status;
use Base\Controllers\WebController;
use App\Packages\Auth\Helpers\AuthSession;

class WebLoginController extends WebController
{
    // Login Views
    private $userLoginView = 'auth.user-login';
    
    // Candidate Login Routes
    private $userLoginSuccessRoute = 'user/dashboard';

    // Employer Login Routes    
    // private $employerLoginSuccessRoute = 'employers/dashboard';

    // Login Failure Routes
    private $loginFailureRoute = 'user/login';

    // Admin Login View
    private $adminLoginView = 'admin.auth.login';

    // Admin Login Routes
    private $adminLoginSuccessRoute = 'admin/dashboard';
    private $adminLoginFailureRoute = 'admin-panel/login';

    // Default Method as Index
    private $defaultIndex = 'user';

    // Login form fields
    private $userIdField = 'phone_number';
    private $passwordField = 'password';
    private $rememberField = 'remember';
    private $appCode = 'sNuQyYTiy';
    private $appDetails;

    public function __construct()
    {
        parent::__construct();

        $this->use->database();

        $this->use->library('form_validation');
        $this->use->library('Authy', null, 'auth');
        $this->use->service('UserService', null, 'user');

    }

    /**
     * Index of auth controller
     * It can be for client or administrator
     *
     * @return void
     */
    public function index()
    {
        $this->{$this->defaultIndex}();
    }

    private function userLoginActive($loginRoute)
    {
        if (AuthSession::userActive()) {
            redirect($loginRoute);
        }
    }

    private function adminLoginActive($loginRoute)
    {
        if (AuthSession::adminActive()) {
            redirect($loginRoute);
        }
    }

    /*-------------------------------- Users Authentication Functionalities ---------------------*/

    /**
     * User Login
     *
     */
    public function user()
    {
        $this->setTitle('Login Page');

        $this->userLoginActive($this->userLoginSuccessRoute);

        validate($this->userIdField, 'Phone Number', 'trim|required');
        validate($this->passwordField, 'Password', 'trim|required');

        if (is('post')) {

            if (form_valid() === false) {
                return layout('users.layouts.auth', $this->userLoginView, $this->data);
            } else {

                $userId = input()->post($this->userIdField, true);
                $password = input()->post($this->passwordField, true);
                
                $this->attemptUserLogin($userId, $password, 'phone_number');
            }

        }

        $this->setTitle('Login Page');
        return layout('users.layouts.auth', $this->userLoginView, $this->data);
    }

    public function autoLogin()
    {

        $this->userLoginActive($this->userLoginSuccessRoute);

        $useUserId = session() ? clean(session('use_userId')) : '';
        $usePassword = session() ? clean(session('use_password')) : '';

        $this->attemptUserLogin($useUserId, $usePassword, 'phone_number');

    }

    private function attemptUserLogin(
        $userId, 
        $password, 
        $index = 'user_id', 
        $table = 'users'
    ) {
        $user = null; // set user variable to null
        $username = null; // set username variable to null
        
        $user = $this->user->existsIn($table, $index, $userId);
        $username = $this->user->existsIn($table, 'username', $userId);
        $email = $this->user->existsIn($table, 'email', $userId);
        
        if (!empty($email)) {
            $username = $email;
        }

        if (!$user && !$username) {
            error_message('User does not exist, Please create an account');
            redirect($this->loginFailureRoute);
        }

        $userId = (!empty($username->user_id)) ? $username->user_id : $user->user_id;
        
        if ($this->auth->login($userId, $password)) {

            $user = $this->user->userDetails(['user_id' => $userId], 'users');

            use_helper('Auth/Util');

            $userGroup = array_pluck(
                $this->auth->getUserRoles($user->user_id),
                'name'
            );
            // dd($userId, $password);
            use_helper('Common/Common');

            remove_session('use_userId');
            remove_session('use_password');

            $session = [
                'fullname' => ucwords($user->title . ' ' .  $user->firstname . ' ' . $user->lastname),
                'firstname' => $user->firstname,
                'user_id' => $user->user_id,
                'user_type' => $user->role,
                'user_group' => $userGroup,
                'user_status' => $user->status,
                'user_image' => $user->image,
                'referral_code' => $user->referral_code,
                'client_session' => true,
                'active' => true,
            ];
            
            // Set User Session
            session($session);

            if ($user->role == 'user') {
                redirect($this->userLoginSuccessRoute);
            }

        } else {

            error_message($this->auth->printErrors());
            redirect($this->loginFailureRoute);
        }
    }

    /*-------------------------------- Administrators Authentication Functionalities ---------------------*/

    /**
     * Admin Login
     */
    public function admin()
    {
        validate($this->userIdField, 'User Id', 'trim|required');
        validate($this->passwordField, 'Password', 'trim|required');

        $this->setTitle('Admin Login Page');
        $this->adminLoginActive($this->adminLoginSuccessRoute);

        if (form_valid() === false) {
            return view($this->adminLoginView, $this->data);
        }

        if (form_valid()) {

            $userId = input()->post($this->userIdField, true);
            $password = input()->post($this->passwordField, true);
            $user = $this->user->existsIn('staff', 'user_id', $userId);
            $username = $this->user->existsIn('staff', 'username', $userId);
            $email = $this->user->existsIn('staff', 'email', $userId);

            if (!empty($email)) {
                $username = $email;
            }

            if (!$user && !$username) {
                error_message('User does not exist, Please Contact System Admin');
                redirect($this->adminLoginFailureRoute);
            }

            $userId = (!empty($username->user_id)) ? $username->user_id : $user->user_id;

            if ($this->auth->login($userId, $password)) {

                $admin = $this->user->userDetails(['user_id' => $userId], 'staff');

                use_helper('Util');

                $adminGroup = array_pluck(
                    $this->auth->getUserRoles($admin->user_id),
                    'name'
                );

                use_helper('Common/Common');

                $session = [
                    'fullname' => ucwords($admin->firstname . ' ' . $admin->lastname),
                    'firstname' => $admin->firstname,
                    'user_id' => $admin->user_id,
                    'user_type' => $admin->role,
                    'user_group' => $adminGroup,
                    'user_status' => $admin->status,
                    'user_image' => $admin->image,
                    'admin_session' => true,
                    'active' => true,
                ];

                // Set User Session
                session($session);

                redirect($this->adminLoginSuccessRoute);
            } else {
                error_message($this->auth->printErrors());
                redirect($this->adminLoginFailureRoute);
            }

            if (strtolower($this->auth->printErrors()) == 'user does not exist') {
                error_message('Please Contact Administrator');
                redirect($this->adminLoginFailureRoute);
            }
        }

        return view($this->adminLoginView, $this->data);
    }

}
