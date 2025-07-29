<?php 


use Base\Http\HttpStatus;
use Base\Actions\CrudAction;
use Base\Helpers\PseudoHash;

class ApiAuthAction extends CrudAction 
{
    private $app;

    public $payload;

    public $userdata;

    public function __construct()
    {
        $this->app = ci();

        $this->use->database(); // enable to use database

        $this->use->library('Auth/Authy', null, 'auth');
        $this->use->library('Auth/AuthToken');

        $this->use->service('Frog/FrogService');

        /** 
         * jwt config file load
         */
        $this->app->use->config('Auth/JWT');
        $this->app->use->config('Auth/JWT');

        $this->use->service('UserService', null, 'user');
    }

    public function model() {}

    //reset password from inside app
    public function resetPasswordInApp($input, $userTable)
    {
        
        $user = $this->user->existsIn($userTable, 'user_id', $input['user_id']);

        $verify = $this->password->verify($input['curr_password'], $user->password);

        if(!$verify){
            return [
                'status' => false,
                'code' => HttpStatus::BAD_REQUEST,
                'reason' => $this->app->lang->line('auth_error_password_invalid')
            ];
        }

        $this->auth->db->where('user_id', $input['user_id']);

        if($userTable === 'staffs'){
            $table = $this->auth->config['admins'];
        }else{
            $table = $this->auth->config['users'];
        }

        $response = $this->auth->db->update($table, [
            'password' => $this->password->hash($input['password'], $input['user_id'])
        ]);

        if(!$response){
            return [
                'status' => false,
                'code' => HttpStatus::BAD_REQUEST,
                'reason' => 'password reset failed'
            ];
        }

        return [
                'status' => false,
                'code' => HttpStatus::OK,
                'data' => true,
                'reason' => 'password reset successful'
            ];
    }
    
    public function loginAdminUser($userId, $password): array
    {

        $auth = new stdClass; //Authenticate();

        // pp($userId);
        $user = $this->user->existsIn('staff', 'user_id', $userId);
        $username = $this->user->existsIn('staff', 'username', $userId);
        $email = $this->user->existsIn('staff', 'email', $userId);

        if (!empty($email)) {
            $username = $email;
        }

        if (!$user && !$username) {
            return [
                'status' => false,
                'code' => HttpStatus::NOT_FOUND,
                'reason' => 'User does not exist, Please Contact System Admin'
            ];
        }

        $userId = (!empty($username->user_id)) ? $username->user_id : $user->user_id;

        $admin = $this->user->userDetails(['user_id' => $userId], 'staff');
        if (!empty($this->returnStatusError($admin))) {
            return $this->returnStatusError($admin);
        }
        if ($this->auth->login($userId, $password)) {
            

            use_helper('Util');

            $this->app->use->library(['Auth/Authy']);
            $authy = new Authy();

            $adminGroup = array_pluck(
                $authy->getUserRoles($admin->user_id),
                'name'
            );

            $payload = [
                'lastname' => $admin->lastname,
                'firstname' =>  $admin->firstname,
                'id' => time(),
                'user_id' => $admin->user_id,
                'user_type' =>  $admin->role,
                'user_image' => $admin->image,
                'ugroup' => 'staff',
                'user_group' => $adminGroup,
                'time' => time(),
                'expire_at' => time() * 60
            ];

            $token = $this->AccessToken->generateToken($payload);
            $userData = array_merge($payload, ['token' => $token]);
            unset($userData['time']);
            unset($userData['expire_at']);

            return [
                'status' => true,
                'code' => HttpStatus::OK,
                'data' => $userData,
                'reason' => 'User login successfull'
            ];
        }
    }

    private function returnStatusError($user)
    {
        if ($user && $user->status === 'set') {
            $response = [
                'status' => false,
                'code' => HttpStatus::UNAUTHORIZED,
                'error' => [
                    'message' => 'Account under review',
                ],
                'data' => [],
                'reason' => 'This account is under review, please try again later'
            ];

            return $response;
        }


        if ($user && $user->status === 'banned') {
            $response = [
                'status' => false,
                'code' => HttpStatus::UNAUTHORIZED,
                'error' => [
                    'message' => 'This account is banned, contact support for assistance',
                ],
                'data' => [],
                'reason' => 'This account is banned'
            ];

            return $response;
        }

        if ($user && $user->status === 'disabled') {
            $response = [
                'status' => false,
                'code' => HttpStatus::UNAUTHORIZED,
                'error' => [
                    'message' => 'Account is disabled',
                ],
                'data' => [],
                'reason' => 'This account is banned, contact support for assistance'
            ];

            return $response;
        }

        return [];
    }

    public function loginUser(array $data): array
    {

        $data = (object)$data;

        $auth = new stdClass; //Authenticate();

        $user = $this->UserModel
            ->select('user_id, email, firstname,lastname, company_id, phone_number, role,image, password, status')
            ->where("email", $data->email)
            ->first();

        if (!empty($this->returnStatusError($user))) {
            return $this->returnStatusError($user);
        }

        if (empty($user)) {
            $response = [
                'status' => false,
                'code' => HttpStatus::NOT_FOUND,
                'error' => [
                    'message' => 'Username or Password not found',
                ],
                'data' => [],
                'reason' => 'Username or Password not found'
            ];

            return $response;
        }

        $verified = $this->auth->login($user->user_id, $data->password, true);

        // pp([$auth->result]);
        if ($verified) {
            $user = $this->UserModel
                ->select('*')
                ->where("email", $user->email)
                ->first();
        } else {
            $response = [
                'status' => false,
                'code' => HttpStatus::NOT_FOUND,
                'error' => [
                    'message' => 'Account not verified',
                ],
                'data' => [],
                'reason' => 'Account not verified'
            ];

            return $response;
        }

        $payload = $this->userdata($user);

        unset($payload['image']);
        $token = $this->AccessToken->generateToken($payload);
        $userData = array_merge($payload, ['image' => $user->image, 'token' => $token]);
        $payload = (object)$payload;

        if ($payload->usertype === 'driver') {
            $userData['vehicle'] = $user->vehicle;
            $userData['vehicle_model'] = $user->vehicle_model;
            $userData['plate_no'] = $user->plate_no;
        }

        $response = [
            'status' => true,
            'code' => HttpStatus::OK,
            'data' => $userData,
            'reason' => 'Logged in successfully'
        ];

        // $response = [
        //     'status' => false,
        //     'code' => HttpStatus::NOT_FOUND,
        //     'error' => [
        //         'message' => 'Email or Password do not match',
        //     ],
        //     'data' => [],
        //     'reason' => 'Email or Password do not match'
        // ];

        return $response;
    }

    public function createUser(object $data): array
    {

        $this->use->library('Auth/Authy', null, 'auth');

        $isAdmin = false;
        $isEmployer = false;
        $userId = $this->user->generateUlid();

        // take a second look at this code.
        if(property_exists($data, 'user_type') && $data->user_type === 'employer') {
            $isEmployer = true;
            $userType = 'employer';
        } else {
            $userType = 'candidate';
        }

        if ($this->auth->userExistByEmail($data->email)) {
            return [
                'status' => false,
                'code' => HttpStatus::BAD_REQUEST,
                'error' => [
                    'message' => "Email account exists already",
                ],
                'data' => [],
                'reason' => "Sorry Email exists already",
            ];
        }
        
        $createdUserAuth = $this->auth->createUser($data->email, $data->password, $data->email, $userId, $isAdmin);
        $addMembership = $this->auth->addMember($userId, $userType);

        // $createdUser = true;
        // $createdUserAuth = true;

        $userdata = [
            'user_id'      => $userId,
            'username'     => $data->email,
            'firstname'    => $data->firstname,
            'lastname'     => $data->lastname,
            'email'        => $data->email,
            'gender'       => $data->gender ?? '',
            'role'         => $userType,
            'image'        => $data->image ?? '',
            'phone_number' => $data->phone ?? '',
            // 'password'     => password_hash($data->password, PASSWORD_BCRYPT),
            'created_at'   => datetime(),
            'created_by'   => $userId
        ];
        
        // dd($userdata);

        // if($isAdmin){
        //     $this->UserModel->table = 'staff'; 
        //     unset($userdata['password']);           
        //     $createdUser = $this->UserModel->save($userdata);
        // }else{
        //     $createdUser = $this->UserModel->save($userdata);
        // }

        $this->UserModel->table = 'users';
        $createdUser = $this->UserModel->create($userdata);

        if (!$createdUser || !$createdUserAuth) {

            log_message('app', "User account not created for " . $userId);

            return [
                'status' => false,
                'code' => HttpStatus::INTERNAL_SERVER_ERROR,
                'error' => [
                    'message' => "User account not created",
                ],
                'data' => [],
                'reason' => "Sorry we're unable to add new user now. Please try again or contact support",
            ];
        }

        $this->use->action('Employers/EmployerAction');

        if ($isEmployer) {
            $data->user_id = $userId;
            $this->saveEmployer($data);
        }

        $this->verifyUser($userdata);

        // event('queue.created.user'); // queue email
        // event('sms.created.user'); // send sms

        return [
            'status' => true,
            'code'   => HttpStatus::CREATED,
            'data'   => $createdUser,
            'reason' => ($isEmployer) ? 'Employer Created Successfully' : 'Candidate Created Successfully'
        ];
        
    }

    public function saveEmployer($employer)
    {
        $this->EmployerAction->model()->save([
            'user_id'       => $employer->user_id,
            'employer_id'   => PseudoHash::encode($employer->employer_name, 6),
            'employer_name'  => $employer->employer_name,
            'employer_phone' => $employer->phone,
            'employer_email' => $employer->email,
            "created_by"    => $employer->user_id,
            "created_at"    => datetime(),
        ]);
    }

    public function verifyUser($user, $isMobleApp = true)
    {

        $user = (object)$user;

        $config = $this->auth->authConfig;

        if ($config['auto.verification']) {

            $verificationCode = $this->auth->getVerificationCode($user->user_id);

            $this->auth->autoVerifyUser($user->user_id, $verificationCode);

            // Send email using Frog
            $frog = app('Frog/FrogService');

            $link = app_url('user/login');

            $from = env('support.email');

            $firstname = $user->firstname;
            $to = $user->phone_number;
            $user_type = $user->role;

            $message = "
                {$user->firstname}, Welcome to Nnoboa, Your {$user_type} account has been created successfully. 
                \nYou can now login with your email and password here. {$link} 
                \nYour verification code is: {$verificationCode}
            ";
                                                            
            $data = (object) [
                'to' => $to,
                'title' => "Registration of {$user_type} with email: ". $user->email,
                'message' => $message
            ];

            $frog->sendSmsMessage($data);

            // Send Email using (Wigal Frog Service)
            $frog->emailDestinations($user->email)
                ->prepareEmail(
                    $from,
                    'Employer Registration with Email ' . $user->email,
                    "{$firstname}, Welcome to Nnoboa, You have successfully created your {$user_type} account, Please click on the link to login with your email and password: " . $link
                    . "\nYour verification code is: {$verificationCode}"
                )->sendEmail();
        }

    }

    private function userdata($user)
    {
        return [
            'user_id' => $user->user_id,
            'id' => time(),
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'company_id' => $user->company_id,
            'phone_number' => $user->phone_number,
            'usertype' => $user->role,
            'image' => $user->image,
            'ugroup' => 'users',
            'status' => $user->status,
            'time' => time(),
            'expire_at' => time() + (60 * 25)
            // 'expire_at' => time() * ((60 * 60) + 15)
        ];
    }

}
/* End of Crud ApiAuthAction file */
