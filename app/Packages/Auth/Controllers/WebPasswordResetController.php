<?php

use Base\Controllers\WebController;

class WebPasswordResetController extends WebController
{
    // User Verify View
    private $forgotPasswordView = 'website.auth.recover-password';

    // User Verify Routes
    private $verifySuccessRoute = 'forgot-password';
    private $verifyFailureRoute = 'forgot-password';

    private $defaultIndex = 'reset';

    private $authConfig;

    public function __construct()
    {
        parent::__construct();

        $this->useDatabase();

        $this->load->library('form_validation');
        $this->load->library('Auth/Authy', null, 'auth');
        $this->load->service('Auth/UserService', null, 'user');

        $this->authConfig = $this->auth->authConfig;
    }

    /**
     * Default function to execute
     * @return void
     */
    public function index()
    {
        $this->{$this->defaultIndex}();
    }

    public function reset()
    {
        $this->setTitle('Forgot Password');
        return layout('website.layouts.index', $this->forgotPasswordView, $this->data);
    
    }

    public function processReset($code)
    {
        $verificationCode = cleanxss($code);

        if (empty($verificationCode)) {
            error_message('Sorry Activation Failed');
            redirect($this->verifyFailureRoute);
        }

        $verified = $this->auth->verifyActivationCode($verificationCode);

        if (empty($verified->verification_code)) {
            error_message('Your Account Is Active Already');
            redirect($this->verifyFailureRoute);
        }

        use_helper('Auth.User');

        $user = user_details(['user_id' => $verified->user_id]);

        if (!$user) {
            error_message('Sorry Activation Failed');
            log_message('app', "User account does not exist with: {$code}");
            redirect($this->verifyFailureRoute);
        }

        if (!isset($verified->verification_expiration)) {
            log_message('app', "Verification expiration does not exist for: {$code}");
            error_message('Sorry Activation Failed');
            redirect($this->verifyFailureRoute);
        }

        $activated = $this->auth->verifyUser($verified->user_id, $verified->verification_code);

        if ($activated) {

            $this->user->changeUserStatus($verified->user_id);

            try {
                $this->auth->sendVerificationSuccessMail($verified->user_id);
                success_message('Account Activated Successfully');
                redirect($this->verifySuccessRoute);
            } catch (\Exception $error) {

                log_message('error', $error->getMessage() . ' in ' . $error->getFile() . ' on line ' . $error->getLine());
                error_message("Sorry, Please try again");
                redirect(current_url());
            }
        }

        error_message('Sorry Activation Failed');
        redirect($this->verifyFailureRoute);
    }

    public function activated()
    {
        // load_view('app.auth.activated');
    }

    public function not_activated()
    {
        // load_view('app.auth.not_activated');
    }

    public function reset_password()
    {
        echo "Your password has been resetted";
    }
}
