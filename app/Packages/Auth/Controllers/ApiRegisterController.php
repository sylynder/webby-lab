<?php

use Base\Http\HttpStatus;
use App\Packages\Auth\Middleware\ApiAuthMiddleware;

class ApiRegisterController extends ApiAuthMiddleware
{

    protected $respond = [];
    // Default Method as Index
    private $defaultIndex = 'client_post';

    // Login post fields
    private $userIdField = 'user_id';
    private $passwordField = 'password';
    private $rememberField = 'remember';

    private $authConfig;
    private $authAction;

    public function __construct()
    {
        parent::__construct();

        $this->use->database();

        $this->use->library('Auth/Authy', null, 'auth');
        $this->use->library('Auth/AuthToken');
        $this->use->action('ApiAuthAction');
        $this->use->service('Auth/UserService', null, 'user');
        $this->use->rule('Auth/ApiLoginRules');

        $this->authAction = app('ApiAuthAction');

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
     * Candidate and Employer Registration
     */
    public function userRegister_post()
    {

        $user = $this->getContent(true);

        $isEmployer = false;

        $this->validate->formData($user);

        $user = (object) $user;

        $this->validate->input('email', 'trim|required|max_length[180]|is_unique[users.email]',[
            'is_unique' => '%s exists already'
        ]);
        $this->validate->input('password', 'trim|required|valid_password|min_length[8]|max_length[50]');
        $this->validate->input('firstname', 'trim|required');
        $this->validate->input('lastname', 'trim|required');
        $this->validate->input('phone', 'trim|required|is_unique[users.phone_number]', [
            'is_unique' => '%s exists already'
        ]);

        if (property_exists($user, 'employer_name')) {
            $this->validate->input('employer_name', 'trim|required|min_length[3]|max_length[180]');
            $user->user_type = "employer";
        }

        // $this->validate->input('user_type', 'trim|required');
        // $this->validate->input('company_id', 'trim|required');
        
        $valid = $this->validate->check();

        if ($valid) {
            $this->respond = $this->authAction->createUser($user); // ['status' => '', 'user' => '', 'name' => '']; //$this->auth->createUser($user);
            $statusCode = ($this->respond["status"]) ? HttpStatus::OK : HttpStatus::BAD_REQUEST;
            $this->response($this->respond, $statusCode);
        }

        if (!$valid) {
            $this->respond = [
                'status' => false,
                'error' => [
                    'code' => HttpStatus::BAD_REQUEST,
                    'message' => $this->validate->error_array(),
                ],
                'reason' => "Invalid User Input"
            ];
            $this->response($this->respond, HttpStatus::BAD_REQUEST);
        }
    }

    /*-------------------------------- Administrators Authentication Functionalities ---------------------*/
    

}
