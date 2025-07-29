<?php

use Base\Http\HttpStatus;
use App\Packages\Auth\Middleware\ApiAuthMiddleware;

class ApiLoginController extends ApiAuthMiddleware
{

    protected $respond = [];
    // Default Method as Index
    private $defaultIndex = 'user_post';

    // Login post fields
    private $userIdField = 'user_id';
    private $passwordField = 'password';
    private $rememberField = 'remember';

    private $authConfig;

    public function __construct()
    {
        parent::__construct();

        $this->useDatabase();

        $this->use->library('form_validation');
        $this->use->library('Auth/Authy', null, 'auth');
        $this->use->library('Auth/AuthToken');
        $this->use->service('Auth/UserService', null, 'user');

        $this->use->rule('Auth/ApiLoginRules');

    }

    /**
     * Index of auth controller
     * It can be for client or administrator
     *
     */
    public function index_post()
    {
        $this->{$this->defaultIndex}();
    }

    /*-------------------------------- Clients Authentication Functionalities ---------------------*/

    /**
     * Client Login
     */
    public function user_post()
    {

        $user = $this->getContent(true);

        validate()->setData($user);

        validate($this->userIdField, 'User Id', 'trim|required');
        validate($this->passwordField, 'Password', 'trim|required');

        if (!form_valid()) {

            $this->response([
                'status' => false,
                'error' => [
                    'code' => HttpStatus::BAD_REQUEST,
                    'message' => validate()->error_array(),
                ],
                'reason' => "Invalid User Input"
            ], HttpStatus::BAD_REQUEST);
        } 

        if (form_valid()) {

            $user = (object)$user;

            $userId = $user->user_id;
            $password = $user->password;

            $user = $this->user->existsIn('users', 'user_id', $userId);
            $username = $this->user->existsIn('users', 'username', $userId);
            $email = $this->user->existsIn('users', 'email', $userId);

            if (!empty($email)) {
                $username = $email;
            }

            if (!$user && !$username) {

                $this->response([
                    'status' => false,
                    'error' => [
                        'code' => HttpStatus::NOT_FOUND,
                        'message' => "User does not exist, Please create an account",
                    ],
                    'reason' => "User does not exist, Please create an account"
                ], HttpStatus::NOT_FOUND);
            }

            $userId = (!empty($username->user_id)) ? $username->user_id : $user->user_id;

            if ($this->auth->login($userId, $password)) {

                $user = $this->user->userDetails(['user_id' => $userId], 'users');

                use_helper('Util');

                $userRoles = array_pluck(
                    $this->auth->getUserRoles($user->user_id),
                    'name'
                );

                try {
                    
                    $user = [
                        'user_id' => $user->user_id,
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname,
                        'email' => $user->email,
                        'user_type' => $user->role,
                        'status' => $user->status,
                    ];

                    $now = time();

                    $payload = [
                        'iss' => app_url(),
                        'aud' => config('app_name'),
                        'iat' => $now,
                        'nbf' => $now + 30,
                        'exp' => 86400,
                        'user' => (array)$user
                    ];

                    $token = $this->AuthToken->generateToken($payload);

                    $this->response([
                        'status' => true,
                        'token' => $token,
                        'user' => $user,
                        'reason' => "User Logged In Successfully"
                    ], HttpStatus::OK);
                } catch(Exception $e) {
                    $this->response([
                        'status' => false,
                        'error' => [
                            'code' => HttpStatus::INTERNAL_SERVER_ERROR,
                            'message' => []
                        ],
                        'reason' => "Sorry something went wrong, Please try again"
                    ], HttpStatus::INTERNAL_SERVER_ERROR);
                }

            } else {

                $this->response([
                    'status' => false,
                    'error' => [
                        'code' => HttpStatus::NOT_FOUND,
                        'message' => $this->auth->printErrors(),
                    ],
                    'reason' => "Wrong Username or Password"
                ], HttpStatus::NOT_FOUND);
            }
        }

    }

    /*-------------------------------- Administrators Authentication Functionalities ---------------------*/
    

}
