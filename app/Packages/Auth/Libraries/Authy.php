<?php

/**
 * Authy is a User Authorization Library initially built for CodeIgniter 3.1.x by Emre Akay as Aauth, 
 * which aims to make easy some essential authenitcation functionalities 
 * such as login, permissions and access operations.
 * 
 * But it has been modified to work as an authentication package for Webby
 * 
 * Despite ease of use, it has also very advanced features like private messages,
 * grouping, access management, public access etc..
 *
 * @author  Emre Akay <emreakayfb@hotmail.com>
 * @contributor Jacob Tomlinson
 * @contributor Tim Swagger (Renowne, LLC) <tim@renowne.com>
 * @contributor Raphael Jackstadt <info@rejack.de>
 * @modified by Kwame Oteng Appiah-Nti <developerkwame@gmail.com>
 *
 * @copyright 2014-2018 Emre Akay
 * @copyright 2021 Kwame Oteng Appiah-Nti
 * @version 3.0.0
 *
 *
 * The old version of Aauth can be obtained from:
 * https://github.com/emreakay/CodeIgniter-Aauth
 *
 * The modified version can be found at
 * https://github.com/sylynder/authy
 * 
 * @todo separate (on some level) the unvalidated users from the "banned" users
 */

use App\Packages\Auth\Helpers\Result;
use App\Packages\Auth\ThirdParty\Google\ReCaptcha;
use App\Packages\Auth\ThirdParty\Google\GoogleAuthenticator;
#[AllowDynamicProperties]
class Authy
{
    /**
     * The CodeIgniter object variable
     * @access public
     * @var object
     */
    public $app;

    /**
     * Variable for loading the config array into
     * @access public
     * @var array
     */
    public $authConfig;

    /**
     * Variable for specifying column for userId
     * @access public
     * @var
     */
    public $userId;

    /**
     * Variable for current user id
     *
     * @access public
     * @var
     */
    public $currentUserId = '';

    /**
     * The CodeIgniter db variable
     * @access public
     * @var object
     */
    public $authDb;

    /**
     * The db config
     * @access public
     * @var 
     */
    public $dbConfig;
    

    /**
     * Array to cache permission-ids.
     * @access private
     * @var array
     */
    private $cachePermissionId;

    /**
     * Array to cache role-ids.
     * @access private
     * @var array
     */
    private $cacheRoleId;

    /**
     * Object for results
     * @access public
     * @var object
     */
    public $result;

    /**
     * Object for Password
     *
     * @var object
     */
    protected $password;

    /**
     * Object for Mailer
     *
     * @var object
     */
    protected $mailer;

    /**
     * Constructor
     */
    public function __construct()
    {
        // get main ci object
        $this->app = &get_instance();

        $this->app->use->library('driver');
        $this->app->use->library('email');

        $this->app->use->config('Auth/Database');
        $this->app->use->config('Auth/Auth');

        $this->queueMail = $this->app->config->item('auth');
        $this->dbConfig = $this->app->config->item('authy_db');

        $this->authConfig = $this->app->config->item('auth');
        $this->authDb = $this->app->use->database($this->dbConfig, true);

        // if sessions are been used
        if ($this->authConfig['use.sessions']) {
            $this->app->use->library('session');
            $this->currentUserId = $this->app->session->userdata('user_id');
        }
          
        $this->app->lang->load('Auth/authy');
        
        // Set userId in users table as the main key
        $this->userId = $this->authConfig['set.user.id.field'] ? 'user_id' : 'id';

        $this->cachePermissionId = [];
        $this->cacheRoleId = [];

        // Pre-Cache IDs
        $this->preCachePermissions();
        $this->preCacheRoles();

        $this->app->use->library('Auth/AuthMailer');
        $this->app->use->library('Auth/Password');
        
        // Initialize Variables
        $this->mailer = new AuthMailer;
        $this->result = new Result($this->authConfig['use.sessions']);
        $this->password = new Password($this->authConfig);

    }

    /**
     * Caches all permission IDs for later use
     *
     * @return mixed
     */
    private function preCachePermissions()
    {

        $query = $this->authDb->get($this->authConfig['permissions']);

        if (empty($query->result())) {
            return [];
        }

        foreach ($query->result() as $row) {
            $key = str_replace(' ', '', trim(strtolower($row->name)));
            $this->cachePermissionId[$key] = $row->id;
        }

        return $this->cachePermissionId;
    }

    /**
     * Caches all role IDs for later use.
     *
     * @return mixed
     */
    private function preCacheRoles()
    {
        $query = $this->authDb->get($this->authConfig['groups']);

        if (empty($query->result())) {
            return [];
        }

        foreach ($query->result() as $row) {
            $key = str_replace(' ', '', trim(strtolower($row->name)));
            $this->cacheRoleId[$key] = $row->id;
        }

        return $this->cacheRoleId;
    }

    /*-----------------------Authentication Functions---------------------------*/

    /**
     * Login user
     * 
     * Check provided details against the db. Add items to 
     * error array on fail, create session if success
     * 
     * @param string $identifier -> userId, email or username
     * @param string $pass
     * @param bool $remember
     * @return bool Indicates successful login.
     */
    public function login($identifier, $password, $remember = false, $totpSecret = null)
    {
        // Remove cookies first
        $cookie = [
            'name' => 'currentuser',
            'value' => '',
            'expire' => -3600,
            'path' => '/',
        ];

        $this->app->input->set_cookie($cookie);

        if ($this->authConfig['ddos.protection'] && !$this->updateLoginAttempts()) {
            $this->result->error($this->app->lang->line('authy_error_login_attempts_exceeded'));
            return false;
        }

        if ($this->authConfig['ddos.protection'] && $this->authConfig['recaptcha.active'] && $this->getLoginAttempts() > $this->authConfig['recaptcha.login.attempts']) {
            $this->app->use->helper('recaptchalib');
            $reCaptcha = new ReCaptcha($this->authConfig['recaptcha.secret']);
            $response = $reCaptcha->verifyResponse($this->app->input->server("REMOTE_ADDR"), $this->app->input->post("g-recaptcha-response"));

            if (!$response->success) {
                $this->result->error($this->app->lang->line('authy_error_recaptcha_not_correct'));
                return false;
            }
        }

        if ($this->authConfig['login.with.username'] == true) {

            if (!$identifier or strlen($password) < $this->authConfig['min'] or strlen($password) > $this->authConfig['max']) {
                $this->result->error($this->app->lang->line('authy_error_login_failed_name'));
                return false;
            }

            $dbIdentifier = 'username';

        } else if ($this->authConfig['login.with.user.id'] == true) {

            if (!$identifier or strlen($password) < $this->authConfig['min'] or strlen($password) > $this->authConfig['max']) {
                $this->result->error($this->app->lang->line('authy_error_login_failed_name'));
                return false;
            }

            $dbIdentifier = 'user_id';

        } else {

            $this->app->use->helper('email');

            if (!(bool)filter_var($identifier, FILTER_VALIDATE_EMAIL) or strlen($password) < $this->authConfig['min'] or strlen($password) > $this->authConfig['max']) {
                $this->result->error($this->app->lang->line('authy_error_login_failed_email'));
                return false;
            }
            
            $dbIdentifier = 'email';
        }

        // if user is not verified
        $query = null;
        $query = $this->authDb->where($dbIdentifier, $identifier);
        $query = $this->authDb->where('banned', 1);
        $query = $this->authDb->where('verification_code !=', '');
        $query = $this->authDb->get($this->authConfig['users']);

        if ($query->num_rows() > 0) {
            $this->result->error($this->app->lang->line('authy_error_account_not_verified'));
            return false;
        }

        // to find user id, create sessions and cookies
        $query = $this->authDb->where($dbIdentifier, $identifier);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->where($dbIdentifier, $identifier);
            $query = $this->authDb->where('banned', 0);
            // $query = $this->authDb->where('verification_code !=', '');
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() == 0) {
            $this->result->error($this->app->lang->line('authy_error_no_user'));
            return false;
        }

        if (
            $this->authConfig['totp.active'] == true
            and $this->authConfig['totp.only.on.ip.change'] == false
            and $this->authConfig['totp.two.step.login.active'] == false
        ) {

            if ($this->authConfig['totp.two.step.login.active'] == true) {
                ($this->authConfig['use.sessions'] === false) 
                    ? $this->result->info(['totp_required' => true])
                    : $this->app->session->set_userdata('totp.required', true);
            }

            $query = null;
            $query = $this->authDb->where($dbIdentifier, $identifier);
            $query = $this->authDb->get($this->authConfig['users']);

            // Check if user is an admin/staff
            if ($query->num_rows() == 0) {
                $query = $this->authDb->where($dbIdentifier, $identifier);
                $query = $this->authDb->get($this->authConfig['admins']);
            }

            $totpSecret = $query->row()->totp_secret;

            if ($query->num_rows() > 0 and !$totpSecret) {
                $this->result->error($this->app->lang->line('authy_error_totp_code_required'));
                return false;
            } else {

                if (!empty($totpSecret)) {
                    $ga = new GoogleAuthenticator();
                    $checkResult = $ga->verifyCode($totpSecret, $totpSecret, 0);
                    
                    if (!$checkResult) {
                        $this->result->error($this->app->lang->line('authy_error_totp_code_invalid'));
                        return false;
                    }
                    
                }
            }
        }

        if (
            $this->authConfig['totp.active'] == true
            and $this->authConfig['totp.only.on.ip.change'] == true
        ) {

            $query = null;
            $query = $this->authDb->where($dbIdentifier, $identifier);
            $query = $this->authDb->get($this->authConfig['users']);

            // Check if user is an admin/staff
            if ($query->num_rows() == 0) {
                $query = $this->authDb->where($dbIdentifier, $identifier);
                $query = $this->authDb->get($this->authConfig['admins']);
            }

            $totpSecret = $query->row()->totp_secret;
            $ipAddress = $query->row()->ip_address;
            $currentIpAddress = $this->app->input->ip_address();

            if ($query->num_rows() > 0 and !$totpSecret) {
                
                if ($ipAddress != $currentIpAddress) {
                    if ($this->authConfig['totp.two.step.login.active'] == false) {
                        $this->result->error($this->app->lang->line('authy_error_totp_code_required'));
                        return false;
                    } else if ($this->authConfig['totp.two.step.login.active'] == true) {
                        ($this->authConfig['use.sessions'] === false) 
                            ? $this->result->info(['totp_required' => true])
                            : $this->app->session->set_userdata('totp.required', true);
                    }
                }

            } else {
                
                if (!empty($totpSecret)) {
                    if ($ipAddress != $currentIpAddress) {
                        $ga = new GoogleAuthenticator();
                        $checkResult = $ga->verifyCode($totpSecret, $totpSecret, 0);
                        if (!$checkResult) {
                            $this->result->error($this->app->lang->line('authy_error_totp_code_invalid'));
                            return false;
                        }
                    }
                }
                
            }
        }

        $query = null;
        $query = $this->authDb->where($dbIdentifier, $identifier);
        $query = $this->authDb->where('banned', 0);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->where($dbIdentifier, $identifier);
            $query = $this->authDb->where('banned', 0);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        $row = $query->row();

        // if email and pass matches and not banned
        $password = ($this->authConfig['use.password.hash'] ? $password : $this->hashPassword($password, $row->id));
        
        if ($query->num_rows() != 0 && $this->verifyPassword($password, $row->password)) {

            // If email and pass matches
            // create session
            $data = [
                // 'id' => $row->id,
                'user_id' => $row->user_id,
                'username' => $row->username,
                'email' => $row->email,
                'loggedin' => true,
            ];
            
            $this->result->userdata = $data;
            $this->result->status = true;

            if ($this->authConfig['use.sessions']) {
                $this->app->session->set_userdata($data);
            }

            if ($remember) {

                $this->app->use->helper('string');
                
                $expire = $this->authConfig['remember'];
                $today = date('Y-m-d');
                $rememberDate = date("Y-m-d", strtotime($today . $expire));
                $randomString = random_string('alnum', 16);
                
                $this->updateRemember($row->user_id, $randomString, $rememberDate);
                
                $cookie = [
                    'name' => 'currentuser',
                    'value' => $row->user_id . "-" . $randomString,
                    'expire' => 99 * 999 * 999,
                    'path' => '/',
                ];
                
                $this->app->input->set_cookie($cookie);
            }

            // update last login
            $this->updateLastLogin($row->user_id);
            $this->updateActivity();

            if ($this->authConfig['remove.successful.attempts'] == true) {
                $this->resetLoginAttempts();
            }

            return true;
        }
        // if not matches
        else {
            $this->result->error($this->app->lang->line('authy_error_login_failed_all'));
            return false;
        }
    }

    /**
     * Check user login
     * Checks if user logged in, also checks remember.
     * @return bool
     */
    public function isLoggedIn($userId = false)
    {

        if ($this->authConfig['use.sessions']) {
            return ($this->app->session->userdata('loggedin')) 
                ? true 
                : false;
        }
        
        $prefix = config_item('cookie_prefix');
        $cookie = $this->app->input->cookie($prefix . 'currentuser', true);
        
        if (!$cookie) {
            return false;
        }

        $userId = preg_replace('/-[^-]*$/', '', $cookie);
        $length = 16;

        $cookie = explode('-', $cookie);
        $cookiekey = array_pop($cookie);

        if (strlen($cookiekey) < $length) {
            return false;
        }
    
        else {
            
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->where('remember_expiration', $cookiekey);
            $query = $this->authDb->get($this->authConfig['users']);

            // Check if user is an admin/staff
            if ($query->num_rows() == 0) {
                $query = $this->authDb->where('user_id', $userId);
                $query = $this->authDb->where('remember_expiration', $cookiekey);
                $query = $this->authDb->get($this->authConfig['admins']);
            }

            $row = $query->row();

            if ($query->num_rows() < 1) {
                $this->updateRemember($userId);
                return false;
            } else {
                if (strtotime($row->remember_time) > strtotime("now")) {
                    $this->loginFast($userId);
                    return true;
                }
                // if time is expired
                else {
                    return false;
                }
            }
        }
        
        return false;
    }

    /**
     * Controls if a logged or public user has permission
     *
     * If user does not have permission to access page, it stops script and gives
     * error message, unless 'no.permission' value is set in config.  If 'no.permission' is
     * set in config it redirects user to the set url and passes the 'no_access' error message.
     * It also updates last activity every time function called.
     *
     * @param bool $permissionParameter If not given just control user logged in or not
     */
    public function control($permissionParameter = false, $userId = false)
    {

        $this->app->use->helper('url');

        if ($this->app->session->userdata('totp.required')) {
            $this->result->error($this->app->lang->line('authy_error_totp_verification_required'));
            redirect($this->authConfig['totp.two.step.login.redirect']);
        }

        $permissionId = $this->getPermissionId($permissionParameter);

        $this->updateActivity();

        if ($permissionParameter == false) {

            if ($this->isLoggedIn()) {
                return true;
            } else if (!$this->isLoggedIn()) {
                $this->result->error($this->app->lang->line('authy_error_no_access'));
                if ($this->authConfig['no.permission'] !== false) {
                    redirect($this->authConfig['no.permission']);
                }
            }
        } else if (!$this->isAllowed($permissionId) or !$this->isRoleAllowed($permissionId)) {

            if ($this->authConfig['no.permission']) {

                $this->result->error($this->app->lang->line('authy_error_no_access'));

                if ($this->authConfig['no.permission'] !== false) {
                    redirect($this->authConfig['no.permission']);
                }
            } else {
                echo $this->app->lang->line('authy_error_no_access');
                die();
            }
        }
    }

    /**
     * Logout user
     * 
     * Destroys the session and remove cookies to log out user.
     * 
     * @return bool If session destroy successful
     */
    public function logout()
    {

        $cookie = [
            'name' => 'currentuser',
            'value' => '',
            'expire' => -3600,
            'path' => '/',
        ];

        $this->app->input->set_cookie($cookie);

        if ($this->authConfig['use.sessions']) {
            return $this->app->session->sess_destroy();
        }

        return true;
    }

    /**
     * Fast login
     * 
     * Login with just a user id
     * 
     * @param string|int $userId User id to log in
     * @return bool true if login successful.
     */
    public function loginFast($userId)
    {

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->where('banned', 0);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->where('banned', 0);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        $row = $query->row();

        if ($query->num_rows() > 0) {

            // if id matches
            // create session
            $data = [
                // 'id' => $row->id,
                'user_id' => $row->user_id,
                'username' => $row->username,
                'email' => $row->email,
                'loggedin' => true,
            ];

            $this->result->userdata = $data;
            $this->result->status = true;

            if ($this->authConfig['use.sessions']) {
                $this->app->session->set_userdata($data);
            }
            
            $this->result->userdata = $data;

            return true;
        }
        
        return false;
    }

    /**
     * Reset last login attempts
     * 
     * Removes a Login Attempt
     * 
     * @return bool Reset fails/succeeds
     */
    public function resetLoginAttempts()
    {
        $ipAddress = $this->app->input->ip_address();
        
        $this->authDb->where([
                'ip_address' => $ipAddress,
                'timestamp >=' => date("Y-m-d H:i:s", strtotime("-" . $this->authConfig['max.login.attempt.time.period'])),
        ]);
        
        return $this->authDb->delete($this->authConfig['login.attempts']);
    }

    /*-----------------------Email Functions---------------------------*/

    /**
     * Remind password
     * 
     * Emails user with link to reset password
     * 
     * @param string $email Email for account to remind
     * @return bool Remind fails/succeeds
     */
    public function remindPassword($email)
    {

        $query = $this->authDb->where('email', $email);
        $query = $this->authDb->get($this->authConfig['users']);

        // check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('email', $email);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($query->num_rows() > 0) {
            
            $row = $query->row();
            $verificationCode = unique_id($this->authConfig['verification.code.length']);
            $data['verification_code'] = $verificationCode;
            
            $this->authDb->where('email', $email);

            if ($isAdmin) {
                $this->authDb->update($this->authConfig['admins'], $data);
            } else {
                $this->authDb->update($this->authConfig['users'], $data);
            }

            if (isset($this->authConfig['email.config']) && is_array($this->authConfig['email.config'])) {
                $this->app->email->initialize($this->authConfig['email.config']);
            }

            $urlSlug = null;

            if ($isAdmin) {
                $urlSlug = $this->authConfig['reset.admin.password.link'];
            } else {
                $urlSlug = $this->authConfig['reset.user.password.link'];
            }

            $mail = [
                'from' => $this->authConfig['app.email'] ?: $this->authConfig['support.email'],
                'name' => $this->authConfig['app.name'],
                'subject' => $this->app->lang->line('authy_email_reset_subject'),
                'message' => $this->app->lang->line('authy_email_reset_text'),
                'verificationCode' => $data['verification_code'],
                'url' => url($urlSlug),
                'view' => $this->authConfig['reset.password.view'],
                'email' => $row->email,
            ];

            $this->sendRemindPasswordEmail($mail);

            return true;
        }
        return false;
    }

    /**
     * Send email to remember user password
     * 
     * @param array $data contains mailing information
     * @return bool
     */
    public function sendRemindPasswordEmail(array $data)
    {
        $this->app->use->service('Auth/UserService');

        $data = (object) $data;
        $user = $this->app->UserService->userDetails(['email' => $data->email]);
        $firstname = $user->firstname;

        $placeholders = [
            'message'          => $data->message,
            'firstname'        => $firstname ?: $this->authConfig['default.firstname'],
            'url'              => $data->url,
            'verificationCode' => $data->verificationCode,
            'subject'          => $data->subject,
            'name'             => $data->name
        ];

        $mail = [
            'from'         => $data->from,
            'fromName'     => $data->name,
            'subject'      => $data->subject,
            'sendTo'       => $data->email,
            'placeholders' => $placeholders,
            'template'     => $data->view,
        ];

        try {
            $this->app->mailer->queueMail($mail);
            return true;
        } catch (Throwable $throw) {
            log_message('app', 'A password reminder mail failed for this email: ' . $data->email);
            return false;
        }
    }

    /**
     * Reset Account With New Password
     * 
     * Generate new password and email it to the user
     * 
     * @param string $verificationCode Verification code for account
     * @return bool Password reset fails/succeeds
     */
    public function resetAccountPassword($verificationCode, $password)
    {
        $query = $this->authDb->where('verification_code', $verificationCode);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('verification_code', $verificationCode);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($query->num_rows() > 0) {

            $row = $query->row();

            $data = [
                'verification_code' => '',
                'password' => $this->hashPassword($password, $row->user_id),
            ];

            if ($this->authConfig['totp.active'] == true and $this->authConfig['totp.reset.over.reset.password'] == true) {
                $data['totp_secret'] = null;
            }

            $email = $row->email;

            $this->authDb->where('user_id', $row->user_id);

            if ($isAdmin) {
                $this->authDb->update($this->authConfig['admins'], $data);
            } else {
                $this->authDb->update($this->authConfig['users'], $data);
            }

            if (isset($this->authConfig['email.config']) && is_array($this->authConfig['email.config'])) {
                $this->app->email->initialize($this->authConfig['email.config']);
            }

            $placeholders = [
                'message'          => $this->app->lang->line('authy_email_reset_success_new_password'),
                'password_message' => $this->app->lang->line('authy_email_reset_success_message') . clean($password),
                'name'             => $this->authConfig['app.name']
            ];

            $data = [
                'from'     => $this->authConfig['app.email'] ?: $this->authConfig['support.email'],
                'fromName' => $this->authConfig['from.name'],
                'subject'  => $this->app->lang->line('authy_email_reset_success_subject'),
                'view'     => $this->authConfig['reset.password.success.view'],
            ];

            $data = (object)$data;

            $mail = [
                'from'         => $data->from,
                'fromName'     => $data->fromName,
                'subject'      => $data->subject,
                'sendTo'       => $email,
                'placeholders' => $placeholders,
                'template'     => $data->view,
            ];

            try {
                $this->app->mailer->queueMail($mail);
                return true;
            } catch (Throwable $throw) {
                log_message('app', 'A new password reset success mail failed for this email: ' . $email);
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Reset password
     * 
     * Generate new password and email it to the user
     * 
     * @param string $verificationCode Verification code for account
     * @return bool Password reset fails/succeeds
     */
    public function resetPassword($verificationCode)
    {

        $query = $this->authDb->where('verification_code', $verificationCode);
        $query = $this->authDb->get($this->authConfig['users']);

        $this->app->use->helper('string');
        $passwordLength = ($this->authConfig['min'] & 1 ? $this->authConfig['min'] + 1 : $this->authConfig['min']);
        $password = random_string('alnum', $passwordLength);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($query->num_rows() > 0) {

            $row = $query->row();

            $data = [
                'verification_code' => '',
                'password'          => $this->hashPassword($password, $row->id),
            ];

            if ($this->authConfig['totp.active'] == true and $this->authConfig['totp.reset.over.reset.password'] == true) {
                $data['totp_secret'] = null;
            }

            $email = $row->email;

            $this->authDb->where('user_id', $row->user_id);

            if ($isAdmin) {
                $this->authDb->update($this->authConfig['admins'], $data);
            } else {
                $this->authDb->update($this->authConfig['users'], $data);
            }

            if (isset($this->authConfig['email.config']) && is_array($this->authConfig['email.config'])) {
                $this->app->email->initialize($this->authConfig['email.config']);
            }

            $placeholders = [
                'message' => $this->app->lang->line('authy_email_reset_success_new_password'),
                'password_message' => $this->app->lang->line('authy_email_reset_success_message') . $password,
            ];

            $data = [
                'from'     => $this->authConfig['app.email'] ?: $this->authConfig['support.email'],
                'fromName' => $this->authConfig['from.name'],
                'subject'  => $this->app->lang->line('authy_email_reset_success_subject'),
                'view'     => $this->authConfig['reset.password.success.view'],
            ];

            $data = (object)$data;

            $mail = [
                'from'         => $data->from,
                'fromName'     => $data->fromName,
                'subject'      => $data->subject,
                'sendTo'       => $email,
                'placeholders' => $placeholders,
                'template'     => $data->view,
            ];

            try {
                $this->app->mailer->queueMail($mail);
                return true;
            } catch (Throwable $throw) {
                log_message('app', 'A password reset success mail failed for this email: ' . $email);
                return false;
            }
        }

        return false;
    }

    /**
     * Set email verification details and
     * Send it to destination
     * 
     * Gather neccessary information to 
     * setup verification email details
     * 
     * @param string $userId 
     * @param string $verificationCode 
     * @return bool
     */
    public function sendUserVerificationMail($userId, $verificationCode)
    {
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;
        $row = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($query->num_rows() > 0) {
            $row = $query->row();
        }

        $verificationCode = $verificationCode;
        $email = $row->email;
        $urlSlug = '';

        if ($isAdmin) {
            $urlSlug = $this->authConfig['reset.admin.password.link'];
        } else {
            $urlSlug = $this->authConfig['verification.link'];
        }

        $this->app->use->service('Auth/UserService');

        $user = $this->app->UserService->userDetails(['user_id' => $userId]);
        $firstname = $user->firstname;

        if (!$verificationCode) {
            return false;
        }

        $placeholders = [
            'message'          => $this->app->lang->line('auth_email_verification_message'),
            'firstname'        => $firstname ?: $this->authConfig['default.firstname'],
            'url'              => url($urlSlug),
            'verificationCode' => $verificationCode,
        ];

        $params = [
            'from'     => $this->authConfig['app.email'] ?: $this->authConfig['support.email'],
            'fromName' => $this->authConfig['app.name'],
            'subject'  => $this->app->lang->line('auth_email_verification_subject'),
            'view'     => $this->authConfig['activation.email.view'],
        ];

        $params = (object)$params;

        $mail = [
            'from'         => $params->from,
            'fromName'     => $params->fromName,
            'subject'      => $params->subject,
            'sendTo'       => $params,
            'placeholders' => $placeholders,
            'template'     => $params->view,
        ];

        $sendTo = $mail['sendTo'];

        try {
            // $this->app->mailer->queue($mail);
            $this->app->mailer->queueMail($mail);
            return true;
        } catch (Throwable $throw) {
            log_message('app', 'A verification mail failed for this email: ' . $sendTo);
            return false;
        }
    }

    /**
     * Set email verification success details and
     * Send it to destination
     * 
     * Gather neccessary information to setup 
     * verification success email details
     * 
     * @param array $userId contains information
     * @return bool
     */
    public function sendUserVerificationSuccessMail($userId)
    {

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;
        $row = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($query->num_rows() > 0) {
            $row = $query->row();
        }

        $email = $row->email;
        $urlSlug = '';

        if ($isAdmin) {
            $urlSlug = $this->authConfig['verification.success.link'];
        } else {
            $urlSlug = $this->authConfig['verification.success.link'];
        }

        $this->app->use->service('Auth/UserService');

        $user = $this->app->UserService->userDetails(['user_id' => $userId]);
        
        $firstname = $user->firstname;

        if (!$user) {
            return false;
        }

        $placeholders = [
            'message'          => $this->app->lang->line('auth_email_verification_success_message'),
            'firstname'        => $firstname ?: $this->authConfig['default.firstname'],
            'url'              => url($urlSlug),
        ];

        $data = [
            'from'     => $this->authConfig['app.email'] ?: $this->authConfig['support.email'],
            'fromName' => $this->authConfig['app.name'],
            'subject'  => $this->app->lang->line('auth_email_verification_success_subject'),
            'view'     => $this->authConfig['activation.success.view'],
        ];

        $data = (object)$data;

        $mail = [
            'from'         => $data->from,
            'fromName'     => $data->fromName,
            'subject'      => $data->subject,
            'sendTo'       => $email,
            'placeholders' => $placeholders,
            'template'     => $data->view,
        ];

        $sendTo = $mail['sendTo'];

        try {
            // $this->app->mailer->queue($mail);
            $this->app->mailer->queueMail($mail);
            return true;
        } catch (Throwable $throw) {
            log_message('app', 'A verification success mail failed for this email: ' . $sendTo);
            return false;
        }
    }

    /**
     * Set email verification details and
     * Send it to destination
     * Gather neccessary information to setup verification email details
     * 
     * @param string $userId 
     * @param string $verificationCode 
     * @return bool
     */
    public function sendVerificationMail($userId, $verificationCode)
    {
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;
        $row = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($query->num_rows() > 0) {
            $row = $query->row();
        }

        $verificationCode = $verificationCode;
        $email = $row->email;
        $urlSlug = '';

        if ($isAdmin) {
            $urlSlug = $this->authConfig['reset.admin.password.link'];
        } else {
            $urlSlug = $this->authConfig['verification.link'];
        }

        $this->app->use->service('Auth/UserService');

        $user = $this->app->UserService->userDetails(['user_id' => $userId]);
        $firstname = $user->firstname;

        if (!$verificationCode) {
            return false;
        }

        $placeholders = [
            'message'          => $this->app->lang->line('authy_email_verification_message'),
            'firstname'        => $firstname ?: $this->authConfig['default.firstname'],
            'url'              => url($urlSlug),
            'verificationCode' => $verificationCode,
        ];

        $data = [
            'from'     => $this->authConfig['app.email'] ?: $this->authConfig['support.email'],
            'fromName' => $this->authConfig['app.name'],
            'subject'  => $this->app->lang->line('authy_email_verification_subject'),
            'view'     => $this->authConfig['activation.email.view'],
        ];

        $data = (object)$data;

        $mail = [
            'from'         => $data->from,
            'fromName'     => $data->fromName,
            'subject'      => $data->subject,
            'sendTo'       => $email,
            'placeholders' => $placeholders,
            'template'     => $data->view,
        ];

        $sendTo = $mail['sendTo'];

        try {
            $this->app->mailer->queueMail($mail);
            return true;
        } catch (Throwable $throw) {
            log_message('app', 'A verification mail failed for this email: ' . $sendTo);
            return false;
        }
    }

    /**
     * Set email verification success details and
     * Send it to destination
     * Gather neccessary information to setup verification success email details
     * 
     * @param array $userId contains information
     * @return bool
     */
    public function sendVerificationSuccessMail($userId)
    {

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;
        $row = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($query->num_rows() > 0) {
            $row = $query->row();
        }

        $email = $row->email;
        $urlSlug = '';

        if ($isAdmin) {
            $urlSlug = $this->authConfig['verification.success.link'];
        } else {
            $urlSlug = $this->authConfig['verification.success.link'];
        }

        $this->app->use->service('Auth/UserService');

        $user = $this->app->UserService->userDetails(['user_id' => $userId]);
        $firstname = $user->firstname;

        if (!$user) {
            return false;
        }

        $placeholders = [
            'message'          => $this->app->lang->line('authy_email_verification_success_message'),
            'firstname'        => $firstname ?: $this->authConfig['default.firstname'],
            'url'              => url($urlSlug),
        ];

        $data = [
            'from'     => $this->authConfig['app.email'] ?: $this->authConfig['support.email'],
            'fromName' => $this->authConfig['app.name'],
            'subject'  => $this->app->lang->line('authy_email_verification_success_subject'),
            'view'     => $this->authConfig['activation.success.view'],
        ];

        $data = (object)$data;

        $mail = [
            'from'         => $data->from,
            'fromName'     => $data->fromName,
            'subject'      => $data->subject,
            'sendTo'       => $email,
            'placeholders' => $placeholders,
            'template'     => $data->view,
        ];

        $sendTo = $mail['sendTo'];

        try {
            $this->app->mailer->queueMail($mail);
            return true;
        } catch (Throwable $throw) {
            log_message('app', 'A verification success mail failed for this email: ' . $sendTo);
            return false;
        }
    }

    /*-----------------------Email Functions End---------------------------*/

    /**
     * Update last login
     * 
     * Update user's last login date
     * 
     * @param int|bool $userId User id to update or false for current user
     * @return bool Update fails/succeeds
     */
    public function updateLastLogin($userId = false)
    {

        if ($userId == false) {
            $userId = $this->currentUserId;
        }

        $data['last_login'] = date("Y-m-d H:i:s");
        $data['ip_address'] = $this->app->input->ip_address();

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($isAdmin) {
            return $this->authDb->update($this->authConfig['admins'], $data);
        } else {
            return $this->authDb->update($this->authConfig['users'], $data);
        }

        return;
    }

    /**
     * Update login attempts
     * 
     * Update login attempt and if exceeds return false
     * 
     * @return bool
     */
    public function updateLoginAttempts()
    {
        $ipAddress = $this->app->input->ip_address();

        $query = $this->authDb->where([
            'ip_address' => $ipAddress,
            'timestamp >=' => date("Y-m-d H:i:s", strtotime("-" . $this->authConfig['max.login.attempt.time.period'])),
        ]);

        $query = $this->authDb->get($this->authConfig['login.attempts']);

        if ($query->num_rows() == 0) {

            $this->authDb->insert($this->authConfig['login.attempts'], [
                "ip_address" => $ipAddress,
                "timestamp" => datetime(),
                "attempts" => 1,
            ]);

            return true;
        } else {

            $row = $query->row();
            $this->authDb->where('id', $row->id);

            $attempts = $row->attempts + 1;

            $this->authDb->update($this->authConfig['login.attempts'], [
                "timestamp" => datetime(),
                "attempts" => $attempts,
            ]);

            if ($attempts > $this->authConfig['max.login.attempt']) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Get login attempt
     * 
     * @return int
     */
    public function getLoginAttempts()
    {
        $ipAddress = $this->app->input->ip_address();
        $query = $this->authDb->where([
            'ip_address' => $ipAddress,
            'timestamp >=' => date("Y-m-d H:i:s", strtotime("-" . $this->authConfig['max.login.attempt.time.period'])),
        ]);

        $query = $this->authDb->get($this->authConfig['login.attempts']);

        if ($query->num_rows() != 0) {
            $row = $query->row();
            return $row->attempts;
        }

        return 0;
    }

    /**
     * Update remember
     * 
     * Update amount of time a user is remembered for
     * 
     * @param string $userId User id to update
     * @param int $expiration
     * @param int $expire
     * @return bool Update fails/succeeds
     */
    public function updateRemember($userId, $expression = null, $expire = null)
    {

        $data['remember_time'] = $expire;
        $data['remember_expiration'] = $expression;

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($isAdmin) {
            $this->authDb->where('user_id', $userId);
            return $this->authDb->update($this->authConfig['admins'], $data);
        } else {
            $this->authDb->where('user_id', $userId);
            return $this->authDb->update($this->authConfig['users'], $data);
        }

        return false;
    }

    /*-----------------------Authentication Functions End---------------------------*/


    /*-----------------------User Functions---------------------------*/

    /**
     * Create user
     * 
     * Creates a new user
     * 
     * @param string $email User's email address
     * @param string $password User's password
     * @param string $username User's username
     * @param string $userId User's generated
     * @return int|bool false if create fails or returns user id if successful
     */
    public function createUser($email = '', $password = '', $username = false, $userId = false, $isAdmin = false)
    {

        $valid = true;

        if ($this->authConfig['login.with.username'] == true) {
            if (empty($username)) {
                $this->result->error($this->app->lang->line('authy_error_username_required'));
                $valid = false;
            }
        }

        if ($this->userExistByUsername($username) && $username != false) {
            $this->result->error($this->app->lang->line('authy_error_username_exists'));
            $valid = false;
        }

        if ($this->userExistByEmail($email)) {
            $this->result->error($this->app->lang->line('authy_error_email_exists'));
            $valid = false;
        }

        if ($this->userExistByUserID($userId)) {
            $this->result->error($this->app->lang->line('authy_error_user_id_exists'));
            $valid = false;
        }

        $validEmail = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$validEmail) {
            $this->result->error($this->app->lang->line('authy_error_email_invalid'));
            $valid = false;
        }

        if (strlen($password) < $this->authConfig['min'] or strlen($password) > $this->authConfig['max']) {
            $this->result->error($this->app->lang->line('authy_error_password_invalid'));
            $valid = false;
        }

        if ($username != false && !ctype_alnum(str_replace($this->authConfig['additional.valid.chars'], '', $username))) {
            $this->result->error($this->app->lang->line('authy_error_username_invalid'));
            $valid = false;
        }

        if (!$valid) {
            return false;
        }

        $data = [
            'email' => $email,
            'password' => $this->hashPassword($password, uniqid(3)), // Password cannot be blank but user_id required for salt, setting bad password for now
            'username' => (!$username) ? '' : $username,
            'user_id' => (!$userId) ? '' : $userId,
            'created_at' => DATETIME,
        ];

        $userCreated = null;

        if ($isAdmin) {
            $userCreated = $this->authDb->insert($this->authConfig['admins'], $data);
        } else {
            $userCreated = $this->authDb->insert($this->authConfig['users'], $data);
        }

        if ($userCreated) {

            $role = '';

            if ($this->authConfig['use.sessions']) {
                $role = session('role');
            }

            if (empty($userId)) {
                $userId = $username;
            }

            // Set user default group
            if ($isAdmin) {
                $role = !empty($role) ? $role : $this->authConfig['admin.group'];
                $this->addMember($userId, $role);
            } else {
                $role = !empty($role) ? $role : $this->authConfig['default.group'];
                $this->addMember($userId, $role);
            }

            // Is verification activated
            // It checks whether user is an admin also. If it does not find
            // it, details will be updated later by re-checking
            if ($this->authConfig['verification'] && !$this->isAdmin()) {
                $data = null;
                $data['banned'] = 1;

                $this->authDb->where('user_id', $userId);

                if ($isAdmin) {
                    $this->authDb->update($this->authConfig['admins'], $data);
                } else {
                    $this->authDb->update($this->authConfig['users'], $data);
                }

                $this->createVerificationCode($userId, $this->authConfig['verification.expire.at']);
            }

            // Update to correct salted password
            if (!$this->authConfig['use.password.hash']) {
                $data = null;
                $data['password'] = $this->hashPassword($password, $userId);
                $this->authDb->where('user_id', $userId);
                $this->authDb->update($this->authConfig['users'], $data);
            }

            return $userId;
        } else {
            return false;
        }
    }

    /**
     * Update user
     * 
     * Updates existing user details
     * 
     * @param string|int $userId User id to update
     * @param string|bool $email User's email address, or false if not to be updated
     * @param string|bool $pass User's password, or false if not to be updated
     * @param string|bool $name User's name, or false if not to be updated
     * @return bool Update fails/succeeds
     */
    public function updateUser($userId, $email = false, $password = false, $username = false)
    {

        $data = [];
        $valid = true;
        $user = $this->getUser($userId);

        if ($user->email == $email) {
            $email = false;
        }

        if ($email != false) {

            if ($this->userExistByEmail($email)) {
                $this->result->error($this->app->lang->line('authy_error_update_email_exists'));
                $valid = false;
            }

            $validEmail = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
            
            if (!$validEmail) {
                $this->result->error($this->app->lang->line('authy_error_email_invalid'));
                $valid = false;
            }

            $data['email'] = $email;
        }

        if ($password != false) {

            if (strlen($password) < $this->authConfig['min'] or strlen($password) > $this->authConfig['max']) {
                $this->result->error($this->app->lang->line('authy_error_password_invalid'));
                $valid = false;
            }

            $data['password'] = $this->hashPassword($password, $userId);
        }

        if ($user->username == $username) {
            $username = false;
        }

        if ($username != false) {

            if ($this->userExistByUsername($username)) {
                $this->result->error($this->app->lang->line('authy_error_update_username_exists'));
                $valid = false;
            }

            if ($username != '' && !ctype_alnum(str_replace($this->authConfig['additional.valid.chars'], '', $username))) {
                $this->result->error($this->app->lang->line('authy_error_username_invalid'));
                $valid = false;
            }
            
            $data['username'] = $username;
        }

        if (!$valid || empty($data)) {
            return false;
        }

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        $isAdmin = false;

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $isAdmin = true;
        }

        if ($isAdmin) {
            $this->authDb->where('user_id', $userId);
            return $this->authDb->update($this->authConfig['admins'], $data);
        } else {
            $this->authDb->where('user_id', $userId);
            return $this->authDb->update($this->authConfig['users'], $data);
        }

        return false;
    }

    /**
     * Get Current User
     * 
     * Get user information 
     * 
     * Use only in session based authentication
     */
    public function userId()
    {
        if ($this->authConfig['use.sessions'] === false) {
            return false;
        }

        return $this->currentUserId;
    }

    /**
     * Get user id field
     * 
     * Get user id field with email or with UserId
     * 
     * @param string|bool $identifier Email or UserId of user
     * @return int|bool User id field
     */
    public function getUserID($identifier = false, $withEmail = false)
    {

        if (!$withEmail) {
            $query = $this->authDb->where('user_id', $identifier ?? $this->currentUserId);
        } else {
            $query = $this->authDb->where('email', $identifier);
        }

        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {

            if (!$withEmail) {
                $query = $this->authDb->where('user_id', $identifier ?? $this->currentUserId);
            } else {
                $query = $this->authDb->where('email', $identifier);
            }

            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() <= 0) {
            $this->result->error($this->app->lang->line('authy_error_no_user'));
            return false;
        }

        return $query->row()->id;
    }
    
    /**
     * Get user
     * 
     * Get user information
     * 
     * @param int|string|bool $userId User id to get or false for current user
     * @return object User information
     */
    public function getUser($userId = false)
    {

        if ($userId == false) {
            $userId = $this->currentUserId;
        }

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() <= 0) {
            $this->result->error($this->app->lang->line('authy_error_no_user'));
            return false;
        }

        return $query->row();
    }

    /**
     * List users
     * 
     * Return users as an object array
     * 
     * @param bool|int $roleParameter Specify group id to list group or false for all users
     * @param string $limit Limit of users to be returned
     * @param bool $offset Offset for limited number of users
     * @param bool $include_banneds Include banned users
     * @param string $sort Order by MYSQL string (e.g. 'name ASC', 'email DESC')
     * @return array Array of users
     */
    public function listUsers($roleParameter = false, $limit = false, $offset = false, $include_banneds = false, $sort = false)
    {

        // if roleParameter is given
        if ($roleParameter != false) {

            $roleParameter = $this->getRoleId($roleParameter);
            $this->authDb->select('*')
                ->from($this->authConfig['users'])
                ->join($this->authConfig['user.groups'], $this->authConfig['users'] . ".id = " . $this->authConfig['user.groups'] . ".user_id")
                ->where($this->authConfig['user.groups'] . ".role_id", $roleParameter);
        } 
        // if roleParameter is not given, lists all users
        else {
            $this->authDb->select('*')
                ->from($this->authConfig['users']);
        }

        // banneds
        if (!$include_banneds) {
            $this->authDb->where('banned != ', 1);
        }

        // order_by
        if ($sort) {
            $this->authDb->order_by($sort);
        }

        // limit
        if ($limit) {

            if ($offset == false) {
                $this->authDb->limit($limit);
            } else {
                $this->authDb->limit($limit, $offset);
            }
        }

        $query = $this->authDb->get();

        return $query->result();
    }

    /**
     * Delete user
     * 
     * Delete a user from db. WARNING Can't be undone
     * 
     * @param int|string $userId User id to delete
     * @return bool Delete fails/succeeds
     */
    public function deleteUser($userId)
    {

        $this->authDb->trans_begin();

        // delete from user_permissions
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['user.permissions']);

        // delete from user_roles
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['user.groups']);

        // delete user variables
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['user.variables']);

        // delete user tokens
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['user.tokens']);
        
        // delete user
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['users']);

        // delete staff
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['admins']);

        if ($this->authDb->trans_status() === false) {
            $this->authDb->trans_rollback();
            return false;
        } else {
            $this->authDb->trans_commit();
            return true;
        }
    }

    /**
     * Soft Delete User
     * 
     * Delete a user from some tables and keep user login details.
     * 
     * @param int|string $userId User id to delete
     * @return bool Delete fails/succeeds
     */
    public function softDeleteUser($userId)
    {

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        $this->authDb->trans_begin();

        // delete from user_permissions
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['user.permissions']);

        // delete from user_roles
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['user.groups']);

        // delete user variables
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['user.variables']);

        // delete user tokens
        $this->authDb->where('user_id', $userId);
        $this->authDb->delete($this->authConfig['user.tokens']);
        
        $data = [
            'verification_code' => '',
            'is_activated' => 0, //added by me
            'banned' => 1,
        ];

        $this->authDb->where('user_id', $userId);

        if ($isAdmin) {
            $this->authDb->update($this->authConfig['admins'], $data);
        } else {
            $this->authDb->update($this->authConfig['users'], $data);
        }

        if ($this->authDb->trans_status() === false) {
            $this->authDb->trans_rollback();
            return false;
        } else {
            $this->authDb->trans_commit();
            return true;
        }
    }

    /**
     * Update activity
     * 
     * Update user's last activity date
     * 
     * @param int|string|bool $userId User id to update or false for current user
     * @return bool Update fails/succeeds
     */
    public function updateActivity($userId = false)
    {

        if ($userId == false) {
            $userId = $this->currentUserId;;
        }

        if ($userId == false) {
            return false;
        }

        $data['last_activity'] = date("Y-m-d H:i:s");

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($isAdmin) {
            return $this->authDb->update($this->authConfig['admins'], $data);
        }

        return $this->authDb->update($this->authConfig['users'], $data);
    }

    /*-----------------------User Functions End---------------------------*/


    /*-----------------------Verification Functions---------------------------*/

    /**
     * Create verification code
     * Create a verification code for user based on user id
     * 
     * @param int|string $userId User id to update verificationCode
     * @param string     $expireAt date for verification code to expire
     * @return bool
     */
    public function createVerificationCode($userId, $expireAt = null)
    {
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($query->num_rows() > 0) {

            $verificationCode = unique_id($this->authConfig['verification.code.length']);

            $data['verification_code'] = $verificationCode;
            $data['verification_expiration'] = date('Y-m-d H:i:s', strtotime('+1 day'));

            if ($expireAt) {
                $data['verification_expiration'] = $expireAt;
            }

            $this->authDb->where('user_id', $userId);

            if ($isAdmin) {
                $this->authDb->update($this->authConfig['admins'], $data);
            } else {
                $this->authDb->update($this->authConfig['users'], $data);
            }

            return true;
        }

        return false;
    }

    /**
     * Get verification code
     * Gets the verification code created 
     * at first time of account creation
     * 
     * @param int|string $userId User id to get verification code
     * @return mixed
     */
    public function getVerificationCode($userId)
    {
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->verification_code;
        }

        return false;
    }

   /**
     * Verify Activaction Code
     * Verifies if activation code exists
     * And returns it's details
     * 
     * @param string $verificationCode code to check verification code exists
     * @return mixed
     */
    public function verifyActivationCode($verificationCode)
    {

        $query = $this->authDb->where('verification_code ', $verificationCode);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() === 0) {
            $query = $this->authDb->where('verification_code ', $verificationCode);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() > 0) {

            $row = $query->row();

            return (object) [
                "user_id"                 => $row->user_id,
                "email"                   => $row->email,
                "verification_code"       => $row->verification_code,
                "verification_expiration" => $row->verification_expiration,
                "is_activated"            => $row->is_activated
            ];
        }

        return false;
    }

    /**
     * Update Verification Time
     * 
     * Allows verification time to be updated
     * 
     * @param int|string $userId
     * @param string $verificationCode
     * @param mixed $expireAt
     * @return bool
     */
    public function updateVerificationTime($userId, $verificationCode, $expireAt = null)
    {
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->where('verification_code', $verificationCode);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() === 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->where('verification_code', $verificationCode);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        // if verification code is true
        if ($query->num_rows() > 0) {

            $data = [
                'verification_expiration' => date('Y-m-d H:i:s', strtotime('+1 day'))
            ];

            if ($expireAt) {
                $data = [
                    'verification_expiration' => $expireAt,
                ];
            }

            $this->authDb->where('user_id', $userId);

            if ($isAdmin) {
                $this->authDb->update($this->authConfig['admins'], $data);
            } else {
                $this->authDb->update($this->authConfig['users'], $data);
            }

            return true;
        }
        
        return false;
    }

    /**
     * Has to Verify
     * 
     * Checks if user has to verify account
     *
     * @param int|string $userId
     * @return bool
     */
    public function hasToVerify($userId)
    {
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() === 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        // if user exists
        if ($query->num_rows() > 0) {

            $row = $query->row();

            $hasTo = false;
            $expireAt = $row->verification_expiration;

            if ($expireAt !== '0000-00-00 00:00:00') {
                $hasTo = true;
            }

            return ($hasTo) ? true : false;
        }
        
        return false;
    }

    /**
     * Verify user
     * 
     * Activates user account based on verification code
     * 
     * @param int|string $userId User id to activate
     * @param string $verificationCode Code to validate against
     * @return bool Activation fails/succeeds
     */
    public function verifyUser($userId, $verificationCode)
    {

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->where('verification_code', $verificationCode);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->where('verification_code', $verificationCode);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        // if verification code is true
        if ($query->num_rows() > 0) {

            $data = [
                'verification_code' => '',
                'verification_expiration' => '0000-00-00 00:00:00',
                'is_activated' => 1, //added by me
                'banned' => 0,
            ];

            $this->authDb->where('user_id', $userId);

            if ($isAdmin) {
                $this->authDb->update($this->authConfig['admins'], $data);
            } else {
                $this->authDb->update($this->authConfig['users'], $data);
            }

            return true;
        }
        
        return false;
    }

    /**
     * Auto verify user
     * 
     * Activates user account based on verification code
     * 
     * This allows user to login immediately but will
     * require user to manually verify mail from time to time
     * until it has been verified
     * 
     * @param int|string $userId User id to activate
     * @param string $verificationCode Code to validate against
     * @return bool Activation fails/succeeds
     */
    public function autoVerifyUser($userId, $verificationCode)
    {

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->where('verification_code', $verificationCode);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->where('verification_code', $verificationCode);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        // If verification code is true
        if ($query->num_rows() > 0) {

            $data = [
                'is_activated' => 1, //added by me
                'banned' => 0,
            ];

            $this->authDb->where('user_id', $userId);

            if ($isAdmin) {
                $this->authDb->update($this->authConfig['admins'], $data);
            } else {
                $this->authDb->update($this->authConfig['users'], $data);
            }

            return true;
        }
        return false;
    }

    /**
     * Ban user
     * 
     * Bans a user account
     * 
     * @param int|string $userId User id to ban
     * @return bool Ban fails/succeeds
     */
    public function banUser($userId)
    {

        $data = [
            'banned' => 1,
            'verification_code' => '',
        ];

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($isAdmin) {
            return $this->authDb->update($this->authConfig['admins'], $data);
        }

        return $this->authDb->update($this->authConfig['users'], $data);
    }

   /**
     * Unban user
     * 
     * Activates user account
     * Same with unlock_user()
     * 
     * @param int|string $userId User id to activate
     * @return bool Activation fails/succeeds
     */
    public function unbanUser($userId)
    {

        $data = [
            'banned' => 0,
        ];

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        $isAdmin = null;

        if ($query->num_rows() == 0) {
            $query = $this->authDb->get($this->authConfig['admins']);
            $isAdmin = true;
        }

        if ($isAdmin) {
            return $this->authDb->update($this->authConfig['admins'], $data);
        }

        return $this->authDb->update($this->authConfig['users'], $data);
    }

    /**
     * Check user banned
     * 
     * Checks if a user is banned
     * 
     * @param int|string $userId User id to check
     * @return bool false if banned, true if not
     */
    public function isBanned($userId)
    {

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->where('banned', 1);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->where('banned', 1);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * User Exist By Username
     * 
     * Check if user exist by username
     * 
     * @param string $name
     *
     * @return bool
     */
    public function userExistByUsername($name)
    {
        $query = $this->authDb->where('username', $name);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('username', $name);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * User Exist By Name Alias To Above
     * 
     * Check if user exist by name
     * 
     * @param string $name
     *
     * @return bool
     */
    public function userExistByName($name)
    {
        return $this->userExistByUsername($name);
    }

    /**
     * User Exist By Email
     * 
     * Check if user exist by user email
     * 
     * @param string $userEmail
     *
     * @return bool
     */
    public function userExistByEmail($userEmail)
    {
        $query = $this->authDb->where('email', $userEmail);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('email', $userEmail);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * User Exist By ID
     * 
     * Check if user exist by ID
     * 
     * @param int $id
     *
     * @return bool
     */
    public function userExistByID($id)
    {
        $query = $this->authDb->where('id', $id);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('id', $id);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * User Exist By ID
     * Check if user exist by user_id
     * 
     * @param string $userId
     *
     * @return bool
     */
    public function userExistByUserID($userId)
    {
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);

        // Check if user is an admin/staff
        if ($query->num_rows() == 0) {
            $query = $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['admins']);
        }

        if ($query->num_rows() > 0) {
            return true;
        }

        return false;
    }

    /*-----------------------Verification Functions End---------------------------*/

    /*-----------------------Password Functions-----------------------------------*/

    /**
     * Hash password
     * 
     * Hash the password for storage in the db
     * 
     * @param string $pass Password to hash
     * @param string|int $userid
     * @return string Hashed password
     */
    public function hashPassword(string $password, string|int $userId)
    {
        return $this->password->hash($password, $userId);
    }

    /**
     * Verifies a password against a previously hashed password.
     *
     * @param string $password The password we're checking
     * @param string $hash     The previously hashed password
     */
    public function verifyPassword($password, $hash)
    {
        return $this->password->verify($password, $hash);
    }

    /*-----------------------Password Functions End---------------------------*/

    /*-----------------------Role Functions-----------------------------------*/

    /**
     * Create role
     * 
     * Creates a new role
     * 
     * @param string $roleName New role name
     * @param string $tags Tag Descriptions of the role
     * @param string $description Description of the role
     * @return int|bool Role id or false on fail
     */
    public function createRole($roleName, $tags = '', $description = '')
    {

        $query = $this->authDb->get_where($this->authConfig['groups'], ['name' => $roleName]);

        if ($query->num_rows() < 1) {

            $data = [
                'name' => slugify($roleName, '_'), // an underscore is used
                'description' => $description,
                'tags_description' => $tags
            ];

            $this->authDb->insert($this->authConfig['groups'], $data);
            $roleId = $this->authDb->insert_id();
            $this->preCacheRoles();
            return $roleId;
        }

        $this->result->info($this->app->lang->line('authy_info_group_exists'));

        return false;
    }

    /**
     * Get user roles
     * 
     * Get roles a user is in
     * 
     * @param int|bool $userId User id to get or false for current user
     * @return array Roles
     */
    public function getUserRoles($userId = false)
    {

        if (!$userId) {
            $userId = $this->currentUserId;
        }

        if (!$userId) {
            $this->authDb->where('name', $this->authConfig['public.group']);
            $query = $this->authDb->get($this->authConfig['groups']);
        } 
        
        if ($userId) {
            $this->authDb->join($this->authConfig['groups'], "id = role_id");
            $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['user.groups']);
        }
        
        return $query->result();
    }

    /**
     * Update role
     * 
     * Change a roles name
     * 
     * @param int $roleParameter Role id to update
     * @param string $roleName New role name
     * @param string $tags Tag Descriptions of the role
     * @param string $description Descriptions of the role
     * @return bool Update success/failure
     */
    public function updateRole($roleParameter, $roleName = false, $tags = false, $description = false)
    {

        $roleId = $this->getRoleId($roleParameter);

        if ($roleName != false) {
            $data['name'] = slugify($roleName, '_');
        }

        if ($tags != false) {
            $data['tags_description'] = $tags;
        }

        if ($description != false) {
            $data['description'] = $description;
        }

        $this->authDb->where('id', $roleId);
        return $this->authDb->update($this->authConfig['groups'], $data);
    }

    /**
     * Delete role
     * 
     * Delete a role from db. WARNING Can't be undone
     * 
     * @param int $roleId User id to delete
     * @return bool Delete success/failure
     */
    public function deleteRole($roleParameter)
    {

        $roleId = $this->getRoleId($roleParameter);

        $this->authDb->where('id', $roleId);
        $query = $this->authDb->get($this->authConfig['groups']);

        if ($query->num_rows() == 0) {
            return false;
        }

        $this->authDb->trans_begin();

        // bug fixed
        // now users are deleted from user_roles table
        $this->authDb->where('role_id', $roleId);
        $this->authDb->delete($this->authConfig['user.groups']);

        $this->authDb->where('role_id', $roleId);
        $this->authDb->delete($this->authConfig['group.permissions']);

        $this->authDb->where('role_id', $roleId);
        $this->authDb->delete($this->authConfig['role.groups']);

        $this->authDb->where('subrole_id', $roleId);
        $this->authDb->delete($this->authConfig['role.groups']);

        $this->authDb->where('id', $roleId);
        $this->authDb->delete($this->authConfig['groups']);

        if ($this->authDb->trans_status() === false) {
            $this->authDb->trans_rollback();
            return false;
        } else {
            $this->authDb->trans_commit();
            $this->preCacheRoles();
            return true;
        }
    }

    /**
     * Add member
     * 
     * Add a user to a role
     * 
     * @param string|int $userId User id to add to role
     * @param int|string $roleParameter Role id or name to add user to
     * @return bool Add success/failure
     */
    public function addMember($userId, $roleParameter)
    {

        $roleId = $this->getRoleId($roleParameter);

        if (!$roleId) {
            $this->result->error($this->app->lang->line('authy_error_no_group'));
            return false;
        }

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->where('role_id', $roleId);
        $query = $this->authDb->get($this->authConfig['user.groups']);

        if ($query->num_rows() < 1) {
            
            $data = [
                'user_id' => $userId,
                'role_id' => $roleId,
            ];

            return $this->authDb->insert($this->authConfig['user.groups'], $data);
        }
        
        $this->result->info($this->app->lang->line('authy_info_already_member'));
        
        return true;
    }

    /**
     * Remove member
     * 
     * Remove a user from a role
     * 
     * @param string|int $userId User id to remove from role
     * @param int|string $roleParameter Role id or name to remove user from
     * @return bool Remove success/failure
     */
    public function removeMember($userId, $roleParameter)
    {
        $roleId = $this->getRoleId($roleParameter);
        $this->authDb->where('user_id', $userId);
        $this->authDb->where('role_id', $roleId);
        return $this->authDb->delete($this->authConfig['user.groups']);
    }

    /**
     * Add subrole
     * 
     * Add a subrole to a role
     * 
     * @param string|int $userId User id to add to role
     * @param int|string $roleId Role id or name to add user to
     * @return bool Add success/failure
     */
    public function addSubRole($roleId, $subRoleId)
    {

        $roleId = $this->getRoleId($roleId);
        $subRoleId = $this->getRoleId($subRoleId);

        if (!$roleId) {
            $this->result->error($this->app->lang->line('authy_error_no_group'));
            return false;
        }

        if (!$subRoleId) {
            $this->result->error($this->app->lang->line('authy_error_no_subgroup'));
            return false;
        }

        $query = $this->authDb->where('role_id', $roleId);
        $query = $this->authDb->where('subrole_id', $subRoleId);
        $query = $this->authDb->get($this->authConfig['role.groups']);

        if ($query->num_rows() < 1) {

            $data = [
                'role_id' => $roleId,
                'subrole_id' => $subRoleId,
            ];

            return $this->authDb->insert($this->authConfig['role.groups'], $data);
        }

        $this->result->info($this->app->lang->line('authy_info_already_subgroup'));

        return true;
    }

    /**
     * Remove subrole
     * 
     * Remove a subrole from a role
     * 
     * @param int|string $roleId Role id or name to remove
     * @param int|string $subRoleId Sub-Role id or name to remove
     * @return bool Remove success/failure
     */
    public function removeSubRole($roleId, $subRoleId)
    {
        $roleId = $this->getRoleId($roleId);
        $subRoleId = $this->getRoleId($subRoleId);
        $this->authDb->where('role_id', $roleId);
        $this->authDb->where('subrole_id', $subRoleId);
        return $this->authDb->delete($this->authConfig['role.groups']);
    }

    /**
     * Remove member
     * 
     * Remove a user from all roles
     * 
     * @param string|int $userId User id to remove from all roles
     * @return bool Remove success/failure
     */
    public function removeMemberFromAll($userId)
    {
        $this->authDb->where('user_id', $userId);
        return $this->authDb->delete($this->authConfig['user.groups']);
    }

    /**
     * Is member
     * 
     * Check if current user is a member of a group
     * Or check if user has a role
     * 
     * @param int|string $roleId Role id or name to check
     * @param int|bool $userId User id, if not given current user
     * @return bool
     */
    public function isMember($roleId, $userId = false)
    {
        // if userId false (or not given), 
        // set current user
        if (!$userId) {
            $userId = $this->currentUserId;
        }

        $roleId = $this->getRoleId($roleId);

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->where('role_id', $roleId);

        $query = $this->authDb->get($this->authConfig['user.groups']);

        // $row = $query->row();

        if ($query->num_rows() > 0) {
            return true;
        }

        return false;

    }

    /**
     * Is admin
     * 
     * Check if current user is a member of the admin group
     * Or check if user has an admin role
     * 
     * @param string|int $userId User id to check, if it is not given checks current user
     * @return bool
     */
    public function isAdmin($userId = false)
    {
        return $this->isMember($this->authConfig['admin.group'], $userId);
    }

    /**
     * Is Super Admin
     * 
     * Check if current user is a member of the super admin group
     * Or check if user has a super admin role
     * 
     * @param string|int $userId User id to check, if it is not given checks current user
     * @return bool
     */
    public function isSuperAdmin($userId = false)
    {
        return $this->isMember($this->authConfig['super.admin.group'], $userId);
    }

    /**
     * List roles
     * 
     * List all roles
     * 
     * @return object Array of roles
     */
    public function listRoles()
    {
        $query = $this->authDb->get($this->authConfig['groups']);
        return $query->result();
    }

    /**
     * Get role details
     * 
     * Get rele details from role id
     * 
     * @param int $roleId Role id to get
     * @return bool|object Role object
     */
    public function getRole($roleId)
    {
        $query = $this->authDb->where('id', $roleId);
        $query = $this->authDb->get($this->authConfig['groups']);

        if ($query->num_rows() == 0) {
            return false;
        }

        $row = $query->row();
        
        return $row;
    }

    /**
     * Get role name
     * 
     * Get role name from role id
     * 
     * @param int $roleId Role id to get
     * @return bool|string role name
     */
    public function getRoleName($roleId)
    {
        $query = $this->authDb->where('id', $roleId);
        $query = $this->authDb->get($this->authConfig['groups']);

        if ($query->num_rows() == 0) {
            return false;
        }

        $row = $query->row();
        
        return $row->name;
    }

    /**
     * Get role id
     * 
     * Get role id from role name or id ( ! Case sensitive)
     * 
     * @param int|string $role_parameter Role id or name to get
     * @return bool|int Role id
     */
    public function getRoleId($roleId)
    {

        if (is_numeric($roleId)) {
            return $roleId;
        }

        $query = $this->authDb->where('name', $roleId);
        $query = $this->authDb->get($this->authConfig['groups']);

        if ($query->num_rows() == 0) {
            return false;
        }

        $row = $query->row();
        
        return $row->id;
    }

    /**
     * Get subroles
     * 
     * Get subroles from role name or id ( ! Case sensitive)
     * 
     * @param int|string $roleId Role id or name to get
     * @return bool|object Array of subRoleId's
     */
    public function getSubRoles($roleId)
    {
        $roleId = $this->getRoleId($roleId);

        $query = $this->authDb->where('role_id', $roleId);
        $query = $this->authDb->select('subrole_id');
        $query = $this->authDb->get($this->authConfig['role.groups']);

        if ($query->num_rows() == 0) {
            return false;
        }

        return $query->result();
    }

    /**
     * List Role Permissions
     * 
     * List all permissions by Role
     * 
     * @param int $roleId Role id or name to check
     * @return bool|object Array of permissions
     */
    public function listRolePermissions($roleId)
    {
        if (empty($roleId)) {
            return false;
        }

        $roleId = $this->getRoleId($roleId);

        $this->authDb->select('*');
        $this->authDb->from($this->authConfig['permissions']);
        $this->authDb->join($this->authConfig['group.permissions'], "permission_id = " . $this->authConfig['permissions'] . ".id");
        $this->authDb->where($this->authConfig['group.permissions'] . '.role_id', $roleId);

        $query = $this->authDb->get();

        if ($query->num_rows() == 0) {
            return false;
        }

        return $query->result();
    }

    /**
     * Get Role Permission Actions
     * 
     * Get available actions a role can perform
     * 
     * @param int $roleId Role id or name to check
     * @return bool|object actions of this role permissions
     */
    public function getRolePermissionActions($roleId = false, $permissionId = false)
    {
        if (!$roleId) {
            return false;
        }

        if (!$permissionId) {
            return false;
        }

        $roleId = $this->getRoleId($roleId);
        $permissionId = $this->getPermissionId($permissionId);

        $query = $this->authDb->select('*')
            ->from($this->authConfig['group.permissions'])
            ->where('permission_id', $permissionId)
            ->where('role_id', $roleId)
            ->get();

        if ($query->num_rows() == 0) {
            return false;
        }

        return $query->row();
    
    }

    /**
     * Is Role allowed
     * 
     * Check if role is allowed to do 
     * specified action, admin always allowed
     * 
     * @param int|string $permissionId Permission id or name to check
     * @param int|string|bool $roleId Role id or name to check, or if false checks all user roles
     * @param string $userId userId to check for permissions
     * @return bool
     */
    public function isRoleAllowed($permissionId, $roleId = false, $userId = false)
    {

        $permissionId = $this->getPermissionId($permissionId);

        // if role parameter is given
        if ($roleId != false) {

            // if role is super_admin group, as admin group has access to all permissions
            if (strcasecmp($roleId, $this->authConfig['admin.group']) == 0) {
                return true;
            }

            $subRoleIds = $this->getSubRoles($roleId);
            $roleId = $this->getRoleId($roleId);

            $query = $this->authDb->where('permission_id', $permissionId);
            $query = $this->authDb->where('role_id', $roleId);
            $query = $this->authDb->get($this->authConfig['group.permissions']);

            $roleAllowed = false;

            if (is_array($subRoleIds)) {

                foreach ($subRoleIds as $role) {
                    if ($this->isRoleAllowed($permissionId, $role->subrole_id)) {
                        $roleAllowed = true;
                    }
                }
            }

            if ($query->num_rows() > 0) {
                $roleAllowed = true;
            }

            return $roleAllowed;
        }
        // if role parameter is not given
        // checks current user's all roles
        else {
            // if public is allowed or he is super_admin
            if (
                $this->isSuperAdmin($userId) or
                $this->isRoleAllowed($permissionId, $this->authConfig['public.group'])
            ) {
                return true;
            }

            // if is not login
            // if (!$this->isLoggedIn()) {
            //     return false;
            // }

            $roleIds = $this->getUserRoles();

            foreach ($roleIds as $role) {
                if ($this->isRoleAllowed($permissionId, $role->id)) {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Is Role Action Available
     * 
     * Check if role has a specified action, super admin always allowed
     * 
     * @param string|array $action Actions to check on
     * @param int|string $permissionId Permission id or name to check
     * @param int|string|bool $roleId Role id or name to check, or if false checks all user roles
     * @param string $userId userId to check for permissions
     * @return bool
     */
    public function isRoleActionAllowed($action, $permissionId, $roleId = false, $userId = false)
    {

        $permissionId = $this->getPermissionId($permissionId);

        // if role parameter is given
        if ($roleId != false) {

            // if role is super_admin group, as admin group has access to all permissions
            if (strcasecmp($roleId, $this->authConfig['admin.group']) == 0) {
                // return true;
            }

            $roleId = $this->getRoleId($roleId);

            $query = $this->authDb->where('permission_id', $permissionId);
            $query = $this->authDb->where('role_id', $roleId);
            $query = $this->authDb->get($this->authConfig['group.permissions']);

            $allowed = $this->actionAllowed($query, $action);

            if ($allowed) {
                return true;
            }

            return false;
        }
        // if role parameter is not given
        // checks current user's all roles
        else {

            // Is SuperAdmin
            // When allowed user becomes too powerful
            if ($this->isSuperAdmin($userId) && $this->authConfig['use.superactions']) {
                return true;
            }

            // if public has actions allowed
            if ($this->isRoleActionAllowed($action, $permissionId, $this->authConfig['public.group'])) {
                return true;
            }

            // if is not login
            // if (!$this->isLoggedIn()) {
            //     return false;
            // }

            $roleIds = $this->getUserRoles();

            foreach ($roleIds as $role) {
                if ($this->isRoleActionAllowed($action, $permissionId, $role->id)) {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Allow Role a Permission
     * 
     * Assign a permission to a role
     * 
     * @param int|string $roleId Role id or name to allow
     * @param int|string $permissionId Permission id or name to allow
     * @param string|array $actions Action given to a role for a particular permisison
     * @return bool Allow success/failure
     */
    public function allowRole($roleId, $permissionId, string $actions = null)
    {

        $permissionId = $this->getPermissionId($permissionId);

        if (!$permissionId) {
            return false;
        }

        $roleId = $this->getRoleId($roleId);

        if (!$roleId) {
            return false;
        }

        $query = $this->authDb->where('role_id', $roleId);
        $query = $this->authDb->where('permission_id', $permissionId);
        $query = $this->authDb->get($this->authConfig['group.permissions']);

        if ($query->num_rows() < 1) {

            $data = [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
                'actions' => $actions
            ];

            return $this->authDb->insert($this->authConfig['group.permissions'], $data);
        }

        return true;
    }

    /**
     * Deny Role a Permission
     * 
     * Remove a permisssion from a role
     * 
     * @param int|string $roleId Role id or name to deny
     * @param int|string $permissionId Permission id or name to deny
     * @return bool Deny success/failure
     */
    public function denyRole($roleId, $permissionId)
    {

        $permissionId = $this->getPermissionId($permissionId);
        $roleId = $this->getRoleId($roleId);

        $this->authDb->where('role_id', $roleId);
        $this->authDb->where('permission_id', $permissionId);

        return $this->authDb->delete($this->authConfig['group.permissions']);
    }

    /*-----------------------Role Functions Ends-----------------------------*/

    /*-----------------------Permissions Functions---------------------------*/

    /**
     * Create permission
     * 
     * Creates a new permission type
     * 
     * @param string $permissionName New permission name
     * @param string $description Permission description
     * @param string $actions Permission Actions 
     * @return int|bool Permission id or false on fail
     */
    public function createPermission($permissionName, $actions = '', $description = '')
    {

        $query = $this->authDb->get_where($this->authConfig['permissions'], ['name' => $permissionName]);

        if ($query->num_rows() < 1) {

            $data = [
                'name' => $permissionName,
                'actions' => $actions,
                'description' => $description,
            ];

            $this->authDb->insert($this->authConfig['permissions'], $data);

            $permissionId = $this->authDb->insert_id();
            $this->precachePermissions();
            
            return $permissionId;
        }

        $this->result->info($this->app->lang->line('authy_info_perm_exists'));

        return false;
    }

    /**
     * Get permission details
     * 
     * Get permission details from permission name or id
     * 
     * @param int|string $permissionId Permission id or name to get
     * @return object  permission or null if permission does not exist
     */
    public function getPermission($permissionId)
    {

        if (is_numeric($permissionId)) {
            $query = $this->authDb->where('id', $permissionId);
        } else {
            $query = $this->authDb->where('name', $permissionId);
        }

        $query = $this->authDb->get($this->authConfig['permissions']);

        if ($query->num_rows() == 0) {
            return false;
        }

        return $query->row();
    }

    /**
     * Get permission id
     * 
     * Get permission id from permission name or id
     * 
     * @param int|string $perm_par Permission id or name to get
     * @return int Permission id or NULL if perm does not exist
     */
    public function getPermissionId($permissionId)
    {

        if (is_numeric($permissionId)) {
            return $permissionId;
        }

        $query = $this->authDb->where('name', $permissionId);
        $query = $this->authDb->get($this->authConfig['permissions']);

        $row = $query->result();

        if (count($row) !== 1) {
            return 0;
        }

        $result = null;
        $result = $row[0];

        return $result->id;
    }

    /**
     * Get user permissions
     * 
     * Get permissions a user has/have
     * 
     * @param int|bool $userId User id to get or false for current user
     * @return array|object Permissions
     * @author developerkwame <Kwame Oteng Appiah-Nti>
     */
    public function getUserPermissions($userId = false)
    {
        // if userId false (or not given), 
        // set current user
        if (!$userId) {
            $userId = $this->currentUserId;
        }

        if ($userId) {
            $this->authDb->select('*, ' . $this->authConfig['user.permissions'] . '.actions AS user_actions');
            $this->authDb->join($this->authConfig['permissions'], "id = permission_id");
            $this->authDb->where('user_id', $userId);
            $query = $this->authDb->get($this->authConfig['user.permissions']);
        }

        return $query->result();
    }

    /**
     * List Permissions
     * 
     * List all permissions
     * 
     * @return array|object Array of permissions
     */
    public function listPermissions()
    {
        $query = $this->authDb->get($this->authConfig['permissions']);
        return $query->result();
    }
    
    /**
     * Update permission
     * 
     * Updates permission name and description
     * 
     * @param int|string $permissionParameter Permission id or permission name
     * @param string $permissionName New permission name
     * @param string $description Permission description
     * @return bool Update success/failure
     */
    public function updatePermission($permissionParameter, $permissionName = false, $description = false)
    {

        $permissionId = $this->getPermissionId($permissionParameter);

        if ($permissionName != false) {
            $data['name'] = $permissionName;
        }

        if ($description != false) {
            $data['description'] = $description;
        }

        $this->authDb->where('id', $permissionId);
        
        return $this->authDb->update($this->authConfig['permissions'], $data);
    }

    /**
     * Delete permission
     * 
     * Delete a permission from db. WARNING Can't be undone
     * 
     * @param int|string $permissionParameter Permission id or perm name to delete
     * @return bool Delete success/failure
     */
    public function deletePermission($permissionParameter)
    {

        $permissionId = $this->getPermissionId($permissionParameter);

        $this->authDb->trans_begin();

        // deletes from role_permissions table
        $this->authDb->where('permission_id', $permissionId);
        $this->authDb->delete($this->authConfig['group.permissions']);

        // deletes from user.permissions table
        $this->authDb->where('permission_id', $permissionId);
        $this->authDb->delete($this->authConfig['user.permissions']);

        // deletes from permission table
        $this->authDb->where('id', $permissionId);
        $this->authDb->delete($this->authConfig['permissions']);

        if ($this->authDb->trans_status() === false) {
            $this->authDb->trans_rollback();
            return false;
        } else {
            $this->authDb->trans_commit();
            $this->precachePermissions();
            return true;
        }
    }

    /**
     * Is user allowed
     * 
     * Check if user allowed to do specified action, admin always allowed
     * first checks user permissions then check role permissions
     * 
     * @param int|string $permissionId Permission id or name to check
     * @param int|string|bool $userId User id to check, or if false checks current user
     * @return bool
     */
    public function isAllowed($permissionId, $userId = false)
    {

        $totpRequired = false;

        $this->app->use->helper('url');

        if ($this->authConfig['use.sessions']) {
            $userId = $this->currentUserId;
            $totpRequired = $this->app->session->userdata('totp.required');
        }

        if ($userId == false) {
            return false;
        }

        if (!$totpRequired) {
            $this->result->error($this->app->lang->line('auth_error_totp_verification_required'));
            redirect($this->authConfig['totp.two.step.login.redirect']);
        }

        if ($this->isSuperAdmin($userId)) {
            return true;
        }

        $permissionId = $this->getPermissionId($permissionId);

        $query = $this->authDb->where('permission_id', $permissionId);
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['user.permissions']);

        if ($query->num_rows() > 0) {
            return true;
        }

        $roleAllowed = false;

        foreach ($this->getUserRoles($userId) as $role) {

            if ($this->isRoleAllowed($permissionId, $role->id)) {
                $roleAllowed = true;
                break;
            }
        }

        return $roleAllowed;
    }

    /**
     * Is user allowed an action
     * 
     * Check if user allowed to do specified action, admin always allowed
     * first checks user permissions then check role permissions
     * 
     * @param string|array $action Actions to check on
     * @param int|string $permissionId Permission id or name to check
     * @param int|bool $userId User id to check, or if false checks current user
     * @return bool
     */
    public function isActionAllowed($action, $permissionId, $userId = false)
    {
        $this->app->use->helper('url');

        if ($this->authConfig['use.sessions']) {
            $userId = $this->currentUserId;
        }

        if ($userId == false) {
            return false;
        }

        // When allowed user becomes too powerful
        if ($this->isSuperAdmin($userId) && $this->authConfig['use.superactions']) {
            return true;
        }

        $permissionId = $this->getPermissionId($permissionId);

        $query = $this->authDb->where('permission_id', $permissionId);
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['user.permissions']);

        $allowed = $this->actionAllowed($query, $action);

        if ($allowed) {
            return true;
        }

        $roleHasAction = false;

        foreach ($this->getUserRoles($userId) as $role) {

            if ($this->isRoleActionAllowed($action, $permissionId, $role->id)) {
                $roleHasAction = true;
                break;
            }
        }

        if ($roleHasAction) {
            return $roleHasAction;
        }

        return false;
    }

    /**
     * Check if action is truely allowed
     *
     * @param mixed $query
     * @param string|array
     * @return bool
     */
    public function actionAllowed($query, $actionGiven)
    {

        $actions = ($query->row(0) !== null) ? $query->row(0)->actions : null;

        $action = !(is_array($actionGiven)) ? (array)$actionGiven : $actionGiven;
        $action = [$action[0]];

        $actions = strtoarr(',', (string)$actions);
        $allowed = (count(array_intersect($action, $actions)) > 0);

        if ($allowed) {
            return true;
        }

        return false;
    }

    /**
     * Allow User
     * 
     * Add User to permission
     * 
     * @param int|string $userId User id to deny
     * @param int|string $permissionId Permission id or name to allow
     * @param string|array $actions Action given to the user for a particular permisison
     * @return bool Allow success/failure
     */
    public function allowUser($userId, $permissionId, string $actions = '')
    {

        $permissionId = $this->getPermissionId($permissionId);

        if (!$permissionId) {
            return false;
        }

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->where('permission_id', $permissionId);
        $query = $this->authDb->get($this->authConfig['user.permissions']);

        // if not inserted before
        if ($query->num_rows() < 1) {

            $data = [
                'user_id' => $userId,
                'permission_id' => $permissionId,
                'actions' => $actions
            ];

            return $this->authDb->insert($this->authConfig['user.permissions'], $data);
        }
        
        return true;
    }

    /**
     * Deny User
     * 
     * Remove user from permission
     * 
     * @param int|string $userId User id to deny
     * @param int|string $permissionId Permission id or name to deny
     * @return bool Deny success/failure
     */
    public function denyUser($userId, $permissionId)
    {

        $permissionId = $this->getPermissionId($permissionId);

        $this->authDb->where('user_id', $userId);
        $this->authDb->where('permission_id', $permissionId);

        return $this->authDb->delete($this->authConfig['user.permissions']);
    }

    /*-----------------------Permissions Functions Ends----------------------------*/

    /*----------------------- Private Message Functions ---------------------------*/

    /**
     * Send Private Message
     * 
     * Send a private message to another user
     * 
     * @param int|string $senderId User id of private message sender
     * @param int|string $receiverId User id of private message receiver
     * @param string $title Message title/subject
     * @param string $message Message body/content
     * @return bool Send successful/failed
     */
    public function sendPrivatMessage($senderId, $receiverId, $title, $message)
    {

        if (!is_numeric($receiverId) or $senderId == $receiverId) {
            $this->result->error($this->app->lang->line('authy_error_self_pm'));
            return false;
        }

        if (
                ($this->isBanned($receiverId)
                || !$this->userExistByID($receiverId))
                || ($senderId && ($this->isBanned($senderId)
                || !$this->userExistByID($senderId)))
        ) {
            $this->result->error($this->app->lang->line('authy_error_no_user'));
            return false;
        }

        if (!$senderId) {
            $senderId = 0;
        }

        if ($this->authConfig['pm.encryption']) {
            $this->app->use->library('encrypt');
            $title = $this->app->encrypt->encode($title);
            $message = $this->app->encrypt->encode($message);
        }

        $data = [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'title' => $title,
            'message' => $message,
            'date_sent' => date('Y-m-d H:i:s'),
        ];

        return $this->authDb->insert($this->authConfig['pms'], $data);
    }

    /**
     * Send multiple Private Messages
     * 
     * Send multiple private messages to other users
     * 
     * @param int|string $senderId User id of private message sender
     * @param array $receiverIds Array of User ids of private message receiver
     * @param string $title Message title/subject
     * @param string $message Message body/content
     * @return array/bool Array with User ID's as key and true or 
     * a specific error message OR false if sender doesn't exist
     */
    public function sendPrivateMessages($senderId, $receiverIds, $title, $message)
    {

        if ($this->authConfig['pm.encryption']) {
            $this->app->use->library('encrypt');
            $title = $this->app->encrypt->encode($title);
            $message = $this->app->encrypt->encode($message);
        }

        if ($senderId && ($this->isBanned($senderId) || !$this->userExistByID($senderId))) {
            $this->result->error($this->app->lang->line('authy_error_no_user'));
            return false;
        }

        if (!$senderId) {
            $senderId = 0;
        }

        if (is_numeric($receiverIds)) {
            $receiverIds = [$receiverIds];
        }

        $return_array = [];

        foreach ($receiverIds as $receiverId) {

            if ($senderId == $receiverId) {
                $return_array[$receiverId] = $this->app->lang->line('authy_error_self_pm');
                continue;
            }

            if ($this->isBanned($receiverId) || !$this->userExistByID($receiverId)) {
                $return_array[$receiverId] = $this->app->lang->line('authy_error_no_user');
                continue;
            }

            $data = [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'title' => $title,
                'message' => $message,
                'date_sent' => date('Y-m-d H:i:s'),
            ];

            $return_array[$receiverId] = $this->authDb->insert($this->authConfig['pms'], $data);
        }

        return $return_array;
    }

    /**
     * List Private Messages
     * 
     * If receiver id not given retruns current user's pms, 
     * if senderId given, it returns only pms from given sender
     * 
     * @param int $limit Number of private messages to be returned
     * @param int $offset Offset for private messages to be returned (for pagination)
     * @param int $senderId User id of private message sender
     * @param int $receiverId User id of private message receiver
     * @return object Array of private messages
     */
    public function listPrivateMessages($limit = 5, $offset = 0, $receiverId = null, $senderId = null)
    {

        if (is_numeric($receiverId)) {
            $query = $this->authDb->where('receiver_id', $receiverId);
            $query = $this->authDb->where('pm_deleted_receiver', null);
        }

        if (is_numeric($senderId)) {
            $query = $this->authDb->where('sender_id', $senderId);
            $query = $this->authDb->where('pm_deleted_sender', null);
        }

        $query = $this->authDb->order_by('id', 'DESC');
        $query = $this->authDb->get($this->authConfig['pms'], $limit, $offset);

        $result = $query->result();

        if ($this->authConfig['pm.encryption']) {
            $this->app->use->library('encrypt');

            foreach ($result as $key => $msg) {
                $result[$key]->title = $this->app->encrypt->decode($msg->title);
                $result[$key]->message = $this->app->encrypt->decode($msg->message);
            }
        }

        return $result;
    }

    /**
     * Get Private Message
     * 
     * Get private message by id
     * 
     * @param int $pmId Private message id to be returned
     * @param int|string $userId User ID of Sender or Receiver
     * @param bool $set_as_read Whether or not to mark message as read
     * @return object Private message
     */
    public function getPrivateMessages($pmId, $userId = null, $set_as_read = true)
    {

        if (!$userId) {
            $userId = $this->currentUserId;
        }

        if (!is_numeric($userId) || !is_numeric($pmId)) {
            $this->result->error($this->app->lang->line('authy_error_no_pm'));
            return false;
        }

        $query = $this->authDb->where('id', $pmId);
        $query = $this->authDb->group_start();
        $query = $this->authDb->where('receiver_id', $userId);
        $query = $this->authDb->or_where('sender_id', $userId);
        $query = $this->authDb->group_end();
        $query = $this->authDb->get($this->authConfig['pms']);

        if ($query->num_rows() < 1) {
            $this->result->error($this->app->lang->line('authy_error_no_pm'));
            return false;
        }

        $result = $query->row();

        if ($userId == $result->receiver_id && $set_as_read) {
            $this->setAsReadPrivateMessages($pmId);
        }

        if ($this->authConfig['pm.encryption']) {
            $this->app->use->library('encrypt');
            $result->title = $this->app->encrypt->decode($result->title);
            $result->message = $this->app->encrypt->decode($result->message);
        }

        return $result;
    }

    /**
     * Delete Private Message
     * 
     * Delete private message by id
     * 
     * @param int $pmId Private message id to be deleted
     * @param int|string $userId User ID
     * @return bool Delete success/failure
     */
    public function deletePrivateMessage($pmId, $userId = null)
    {
        if (!$userId) {
            $userId = $this->currentUserId;
        }

        if (!is_numeric($userId) || !is_numeric($pmId)) {
            $this->result->error($this->app->lang->line('authy_error_no_pm'));
            return false;
        }

        $query = $this->authDb->where('id', $pmId);
        $query = $this->authDb->group_start();
        $query = $this->authDb->where('receiver_id', $userId);
        $query = $this->authDb->or_where('sender_id', $userId);
        $query = $this->authDb->group_end();
        $query = $this->authDb->get($this->authConfig['pms']);
        $result = $query->row();

        if ($userId == $result->sender_id) {

            if ($result->pm_deleted_receiver == 1) {
                return $this->authDb->delete($this->authConfig['pms'], ['id' => $pmId]);
            }

            return $this->authDb->update(
                $this->authConfig['pms'], 
                ['pm_deleted_sender' => 1], 
                ['id' => $pmId]
            );
            
        } else if ($userId == $result->receiver_id) {

            if ($result->pm_deleted_sender == 1) {
                return $this->authDb->delete($this->authConfig['pms'], ['id' => $pmId]);
            }

            return $this->authDb->update(
                $this->authConfig['pms'], 
                ['pm_deleted_receiver' => 1, 'date_read' => date('Y-m-d H:i:s')], 
                ['id' => $pmId]
            );
            
        }
    }

    /**
     * Cleanup PMs
     * 
     * Removes PMs older than 'pm.cleanup.max.age' (defined in authy config).
     * recommend for a cron job
     * 
     * @return mixed
     */
    public function cleanupPrivateMessages()
    {
        $pm_cleanup_max_age = $this->authConfig['pm.cleanup.max.age'];
        $date_sent = date('Y-m-d H:i:s', strtotime("now -" . $pm_cleanup_max_age));
        $this->authDb->where('date_sent <', $date_sent);

        return $this->authDb->delete($this->authConfig['pms']);
    }

    /**
     * Count unread Private Message
     * 
     * Count number of unread private messages
     * 
     * @param int|bool $receiverId User id for message receiver, if false returns for current user
     * @return int Number of unread messages
     */
    public function countUnreadPrivateMessages($receiverId = false)
    {

        if (!$receiverId) {
            $receiverId = $this->currentUserId;
        }

        $query = $this->authDb->where('receiver_id', $receiverId);
        $query = $this->authDb->where('date_read', null);
        $query = $this->authDb->where('pm_deleted_sender', null);
        $query = $this->authDb->where('pm_deleted_receiver', null);
        $query = $this->authDb->get($this->authConfig['pms']);

        return $query->num_rows();
    }

    /**
     * Set Private Message as read
     * 
     * Set private message as read
     * 
     * @param int $pmId Private message id to mark as read
     */
    public function setAsReadPrivateMessages($pmId)
    {

        $data = [
            'date_read' => date('Y-m-d H:i:s'),
        ];

        $this->authDb->update($this->authConfig['pms'], $data, "id = $pmId");
    }

    /*----------------------- Private Message Functions Ends ---------------------------*/

    /*----------------------- User Variables Functions ---------------------------*/

    /**
     * Set User Variable as key value
     * 
     * if variable not set before, it will be set
     * if set, overwrites the value
     * 
     * @param string $key
     * @param string $value
     * @param int|string $userId; if not given current user
     * @return bool
     */
    public function setUserVar($key, $value, $userId = false)
    {

        if (!$userId) {
            $userId = $this->currentUserId;
        }

        // if specified user is not found
        if (!$this->getUser($userId)) {
            return false;
        }

        // if var not set, set
        if ($this->getUserVar($key, $userId) === false) {

            $data = [
                'data_key' => $key,
                'value' => $value,
                'user_id' => $userId,
            ];

            return $this->authDb->insert($this->authConfig['user.variables'], $data);
        }
        // if var already set, overwrite
        else {

            $data = [
                'data_key' => $key,
                'value' => $value,
                'user_id' => $userId,
            ];

            $this->authDb->where('data_key', $key);
            $this->authDb->where('user_id', $userId);

            return $this->authDb->update($this->authConfig['user.variables'], $data);
        }
    }

    /**
     * Unset User Variable as key value
     * 
     * @param string $key
     * @param int|string $userId ; if not given current user
     * @return bool
     */
    public function unsetUserVar($key, $userId = false)
    {

        if (!$userId) {
            $userId = $this->currentUserId;
        }

        // if specified user is not found
        if (!$this->getUser($userId)) {
            return false;
        }

        $this->authDb->where('data_key', $key);
        $this->authDb->where('user_id', $userId);

        return $this->authDb->delete($this->authConfig['user.variables']);
    }

    /**
     * Get User Variable by key
     * 
     * Return string of variable value or false
     * 
     * @param string $key
     * @param int|string $userId ; if not given current user
     * @return bool|string , false if var is not set, the value of var if set
     */
    public function getUserVar($key, $userId = false)
    {

        if (!$userId) {
            $userId = $this->currentUserId;
        }

        // if specified user is not found
        if (!$this->getUser($userId)) {
            return false;
        }

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->where('data_key', $key);
        $query = $this->authDb->get($this->authConfig['user.variables']);

        // if variable not set
        if ($query->num_rows() < 1) {
            return false;
        } else {

            $row = $query->row();
            return $row->value;
        }
    }

    /**
     * Get User Variables by user id
     * 
     * Return array with all user keys & variables
     * 
     * @param int|string $userId ; if not given current user
     * @return bool|array , false if var is not set, the value of var if set
     */
    public function getUserVars($userId = false)
    {

        if (!$userId) {
            $userId = $this->currentUserId;
        }

        // if specified user is not found
        if (!$this->getUser($userId)) {
            return false;
        }

        $query = $this->authDb->select('data_key, value');
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['user.variables']);

        return $query->result();
    }

    /**
     * List User Variable Keys by UserID
     * 
     * Return array of variable keys or false
     * 
     * @param int|string $userId ; if not given current user
     * @return bool|array, false if no user vars, otherwise array
     */
    public function listUserVarKeys($userId = false)
    {

        if (!$userId) {
            $userId = $this->currentUserId;
        }

        // if specified user is not found
        if (!$this->getUser($userId)) {
            return false;
        }

        $query = $this->authDb->select('data_key');
        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['user.variables']);

        // if variable not set
        if ($query->num_rows() < 1) {
            return false;
        } else {
            return $query->result();
        }
    }

    /**
     * Print Errors
     *
     * Prints string of errors separated by delimiter
     * 
     * @param string $divider Separator for errors
     */
    public function printErrors($divider = '<br />')
    {
        return $this->result->printErrors($divider);
    }

    /*----------------------- Totp Recaptcha Functions ---------------------------*/

    /**
     * Generate Recaptcha field
     * 
     * Return string of the generated recaptcha content
     * 
     * @param int $loginAttemts get from Authenticate->getLoginAttermpst()
     * @return string
     */
    public function generateRecaptchaField($loginAttemts = 0)
    {

        if ($loginAttemts == 0) {
            $loginAttemts = $this->getLoginAttempts();
        }

        $content = '';

        if (
            $this->authConfig['ddos.protection']
            && $this->authConfig['recaptcha.active']
            && $loginAttemts >= $this->authConfig['recaptcha.login.attempts']
        ) {
            $content .= "<script type='text/javascript' src='https://www.google.com/recaptcha/api.js'></script>";
            $siteKey = $this->authConfig['recaptcha.site.key'];
            $content .= "<div class='g-recaptcha' data-sitekey='{$siteKey}'></div>";
        }

        return $content;
    }

   /**
     * Update User Totp Secret
     * 
     * Updates a user's totp secret by the user's Id and secret
     * 
     * @param int|string $userId ; if not given current user
     * @return int|bool id of inserted row or false
     */
    public function updateUserTotpSecret($userId = false, $secret = '')
    {

        if ($this->authConfig['use.sessions']) {
            $userId = $this->currentUserId;
        }
        
        if ($userId == false) {
            return false;
        }
        
        $data['totp_secret'] = $secret;

        $this->authDb->where('user_id', $userId);
        return $this->authDb->update($this->authConfig['users'], $data);
    }

    /**
     * Generate Unique Totp Secret
     * 
     * Generates a unique totp secret
     * 
     * @return string
     */
    public function generateUniqueTotpSecret()
    {
        $ga = new GoogleAuthenticator();
        $stop = false;

        while (!$stop) {
            $secret = $ga->createSecret();
            $query = $this->authDb->where('totp_secret', $secret);
            $query = $this->authDb->get($this->authConfig['users']);

            if ($query->num_rows() == 0) {
                return $secret;
                $stop = true;
            }
        }
    }

    /**
     * Generate Totp QrCode
     * 
     * Generate Totp QrCode using Secret
     * 
     * @param string $secret;
     * @return string, generated qrcode url
     */
    public function generateTotpQrcode($secret)
    {
        $ga = new GoogleAuthenticator();
        return $ga->getQRCodeGoogleUrl($this->authConfig['name'], $secret);
    }

    /**
     * Verify User Totp Code
     * 
     * Return true|false if code is verified
     * 
     * @param int|string $totpcode ;
     * @param string|bool $userId ; if not given use current user
     * @return bool, false if totp not required | if totpSecret is empty
     */
    public function verifyUserTotpCode($totpSecret, $userId = false)
    {
        if (!$this->isTotpRequired()) {
            return true;
        }

        if ($this->authConfig['use.sessions']) {
            $userId = $this->currentUserId;
        }
        
        if ($userId == false) {
            return false;
        }

        if (empty($totpSecret)) {
            $this->result->error($this->app->lang->line('authy_error_totp_code_required'));
            return false;
        }

        $query = $this->authDb->where('user_id', $userId);
        $query = $this->authDb->get($this->authConfig['users']);
        
        $totpSecret = $query->row()->totp_secret;
        $ga = new GoogleAuthenticator();
        $checkResult = $ga->verifyCode($totpSecret, $totpSecret, 0);
        
        if (!$checkResult) {
            $this->result->error($this->app->lang->line('authy_error_totp_code_invalid'));
            return false;
        }
        
        if ($this->authConfig['use.sessions']) {
            $this->app->session->unset_userdata('totp.required');
        }

        return true;
    }

    /**
     * Get Totp Status
     *
     * @return bool|array
     */
    public function setTotpRequired($status = false)
    {

        if ($this->authConfig['use.sessions']) {
            $status = $this->app->session->userdata('totp.required');
        }

        return $status;
    }

    /**
     * Check if Totp is required
     * 
     * Return true|false if required or not
     * 
     * @param bool $totpRequired
     * @return bool
     */
    public function isTotpRequired()
    {

        $totpRequired = $this->setTotpRequired();

        if ($totpRequired) {
            return true;
        }
        
        return false;
    }
    
}
/* end of Authy file */
/* Location: ./Auth/Libraries/Authy.php */
