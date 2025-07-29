<?php

use Base\Controllers\WebController;
use Base\Helpers\PseudoHash;

class WebRegisterController extends WebController
{
    // User Register View
    private $userRegisterView = 'auth.user-register';

    private $autoLoginRoute= 'auth.auto-login';
    // User Register Routes
    private $userRegisterSuccessRoute = 'user.register';
    private $userRegisterFailureRoute = 'user.register';

    // Employer Register View
    private $employerRegisterView = 'auth.employer-signup';
    // private $employerRegisterView = 'Auth.users.register';

    // User Register Routes
    private $employerRegisterSuccessRoute = 'employers.register';
    private $employerRegisterFailureRoute = 'employers.register';

    private $defaultIndex = 'user';

    private $notify;

    private $autoLogin = false;
    private $authConfig;

    public function __construct()
    {
        parent::__construct();

        $this->useDatabase();
        
        $this->use->library('form_validation');
        $this->use->library('Auth/Authy', null, 'auth');
        $this->use->service('Auth/UserService');
        // $this->use->service('Frog/FrogService');
        $this->use->helper('Common/Common');
        
        $this->authConfig = $this->auth->authConfig;

        $this->notify = app('Auth/NotificationAction');
    }

    /**
     * Default function to execute
     * @return void
     */
    public function index()
    {
        $this->{$this->defaultIndex}();
    }

    /**
     * A way to add your new register
     * fields without touching the client/user method
     * 
     * Allows needed input fields to be added
     *
     * These fields are already provided
     * [user_id, username, email]
     * 
     * You can override them with the array
     * fields you will provide here,
     * make sure you validate your input
     * 
     * e.g. validate('phone_number', 'Phone', 'trim|requiered');
     * 
     * Read the documentation to know how to use this method
     * @return array
     */
    private function registerFields()
    {
        // Add the missing fields needed
        // per your implementation
        // from the documentation
        return [
            'phone_number' => input()->post('phone_number', true),
        ];
    }

    public function user()
    {

        validate()->input('user_field', 'trim|honey_check');
        validate()->input('app_time', 'trim|honey_time[5]');
        validate('phone_number', 'Phone Number', 'trim|required|min_length[10]|is_unique[users.phone_number]', [
            'is_unique' => 'Phone Number exists already'
        ]);
        validate('email', 'Email', 'trim|required|valid_email|is_unique[users.email]', [
            'valid_email' => 'Please enter a valid email',
            'is_unique' => 'Email exists already'
        ]);
        validate('password', 'Password', 'trim|required|valid_password|min_length[8]|max_length[18]');
        validate('confirm_password', 'Confirm Password', 'trim|required|matches[password]|min_length[8]|max_length[18]', [
            'matches' => 'Password does not match'
        ]);

        $registerFields = clean($this->registerFields());

        if ($this->auth->userExistByEmail(clean(post('email')))) {
            set_error('email', 'Email exists already');
        }

        if (form_valid() === false) {
            $this->setTitle('User Register');
            return layout('users.layouts.auth', $this->userRegisterView, $this->data);
        }

        if (form_valid()) {
            
            $ulid = $this->UserService->generateUlid();

            // Add any new fields that 
            // your project requires after role index
            // eg firstname, lastname, phone_number
            $user = [
                'user_id'  => $ulid,
                'email'    => input()->post('email', true),
                'role'    => input()->post('user_type', true),
                'password'    => input()->post('password', true),
                'username' => input()->post('email', true),
                'referral_code' => PseudoHash::encode($ulid, 8),
            ];

            $user = $registerFields + $user;

            // A workaround for when firstname 
            // is not available in this situation
            $user['firstname'] = isset($user['firstname'])
                ? $user['firstname']
                : $this->authConfig['default.firstname'];
            
            $user['role'] = $this->authConfig['default.group'];

            if ($this->UserService->existsIn('users', 'email', $user['email'])) {
                error_message("Sorry, An account with: {$user['email']}, exists already");
                redirect($this->userRegisterFailureRoute);
            }

            unset($user['password']);

            if ($this->UserService->createUser($user, 'users')) {

                $user['password'] = input()->post('password', true);

                $user = objectify($user);

                try {

                    $userId = $this->auth->createUser($user->email, $user->password, $user->username, $user->user_id);
                    $this->auth->addMember($user->user_id, $user->role);

                    if ($this->authConfig['auto.verification']) {

                        $verificationCode = $this->auth->getVerificationCode($userId);

                        $this->auth->autoVerifyUser($userId, $verificationCode);

                        // success_message('You have registered successfully, an email has been sent to your inbox for verification');
                        success_message('You have registered successfully, you can now login into your account with Phone Number and Password');

                        $link = app_url('user/login');

                        // $firstname = input()->post('firstname', true);
                        $to = input()->post('phone_number', true);
                        $user_type = input()->post('user_type', true);
                        $app_name = config('app_name');

                        $smsMessage = "
                            Welcome to {$app_name}, Your account has been created successfully. 
                            \nYou can now login with your email and password here. {$link} 
                            \nYour verification code is: {$verificationCode}
                        ";

                        $emailMessage = "Welcome to {$app_name}, You have successfully created your account, 
                                \nPlease click on the link to login with your Phone Number and password: " . $link
                                ."\nYour verification code is: {$verificationCode}";
                        
                        $data = (object) [
                            'to' => $to,
                            'title' => "Registration of {$user_type} with email: ". $user->email,
                            'message' => $smsMessage,
                            'body' => $emailMessage,
                            'from' => env('support.email'),
                            'email' => $user->email,
                            'verificationCode' => $verificationCode
                        ];
                        
                        $this->notify->userRegistered($data);

                    }

                    if (!$this->authConfig['auto.verification'] && !$this->authConfig['verification']) {
                        success_message('You have registered successfully');
                    } else if (!$this->authConfig['auto.verification'] && $this->authConfig['verification']) {

                        success_message('You have registered successfully, you can now login into your account with Phone Number and Password');
                        
                    }
                } catch (Exception $error) {

                    log_message('error', $error->getMessage() . ' in ' . $error->getFile() . ' on line ' . $error->getLine());
                    error_message("Sorry, An error occured, please try again");
                    redirect($this->userRegisterFailureRoute);
                }

                if ($this->authConfig['auto.login']) {
                    
                    session([
                        'use_userId' => $user->phone_number,
                        'use_password' => $user->password
                    ]);

                    redirect($this->autoLoginRoute);
                }

                redirect($this->userRegisterSuccessRoute);
                exit;
            }
        }

        $this->setTitle('User Register');
        return layout('users.layouts.auth', $this->userRegisterView, $this->data);
    }

}
