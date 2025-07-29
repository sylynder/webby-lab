<?php

use App\Enums\Status;
use Base\Helpers\Uuid;
use Base\Helpers\Ulid;
use App\Packages\Auth\Enums\UserState;
use App\Packages\File\Libraries\Upload;

class UserService extends \Base_Service
{

    private $upload;
    private $authConfig = [];
    private $userConfig = [];
    private $staff = null;
    private $user = null;
    private $admin = 'admin';
    public $temporalPassword = '';

    public function __construct()
    {
        $this->use->config('Auth/User');
        $this->use->config('Auth/Auth');
        $this->use->model('Auth/UserModel');

        $this->authConfig = $this->config->item('auth');
        $this->userConfig = (object)$this->config->item('user');
        $this->staff = $this->userConfig->staff_table;
        $this->user = $this->userConfig->user_table;

        $this->upload = new Upload();
        
    }

    /**
     * Generate Uuids for Users
     *
     * @return string
     */
    public function generateUuid()
    {
        return Uuid::v4();
    }

    /**
     * Generate Ulids for Users
     *
     * @param boolean $toLowerCase
     * @return string
     */
    public function generateUlid($toLowerCase = false)
    {
        return (string) Ulid::generate($toLowerCase);
    }

    /**
     * Get User Config
     *
     * @return mixed
     */
    public function userConfig() {
        return $this->userConfig;
    }

    public function model($table = 'users')
    {
        if (!is_null($table)) {
            $this->UserModel->table = $table;
        } else {
            $this->UserModel->table = $this->user;
        }

        return $this->UserModel;
    }

    /**
     * Generate User Id using provided Email
     *
     * @param string $email
     * @return string
     */
    public function generateIdWithEmail($email)
    {
        $substr_email = substr(extract_email_name($email), ZERO, SIX);
        return hash_algo('sha1', $substr_email . unique_code(20));
    }

    /**
     * Get user profile image
     *
     * @param string $userId
     * @return string
     */
    public function getUserProfileImage($userId)
    {

        $this->UserModel->table = $this->user;

        $profileImage = $this->UserModel->select('image')
            ->where(['user_id' => $userId])
            ->first();

        if (is_null($profileImage)) {
            return '';
        }

        return $profileImage->image;
    }

    /**
     * Get staff profile image
     *
     * @param string $userId
     * @return string
     */
    public function getStaffProfileImage($userId)
    {

        $this->UserModel->table = $this->staff;

        $profileImage = $this->UserModel->select('image')
            ->where(['user_id' => $userId])
            ->first();

        if (is_null($profileImage)) {
            return '';
        }

        return $profileImage->image;
    }

    /**
     * Upload User Profile Image
     *
     * @param array $image
     * @param string $userId
     * @param bool $isStaff
     * @return string
     */
    public function uploadProfileImage($image, $userId, $isStaff = false)
    {

        $imagepath = $this->userConfig->user_profile_image;
        $source = $this->userConfig->user_table;

        if ($isStaff) {
            $imagepath = $this->userConfig->staff_profile_image;
            $source = $this->userConfig->staff_table;
        }

        if (!is_array($image)) {
            return '';
        }

        if ($image['name'] !== "") {

            $profileImage = $this->upload->uploadImage(
                $image,
                $imagepath,
                md5($userId . 'profile_image')
            );

            $user = [
                'user_id' => $userId,
                'image' => $profileImage
            ];

            $this->editUser($user, $source);

            return $profileImage;
        }

        return '';
    }

    /**
     * Delete Profile Image
     *
     * @param string $image
     * @param bool $isStaff
     * @return bool
     */
    public function deleteProfileImage($image, $isStaff = false)
    {
        $imagepath = $this->userConfig->user_profile_image;

        if ($isStaff) {
            $imagepath = $this->userConfig->staff_profile_image;
        }

        $image = $imagepath . $image;

        if (file_exists($image)) {
            unlink($image);
            return true;
        }

        return false;
    }

    /**
     * Get user info from table
     *
     * @param string $table
     * @param string $column
     * @param string $value
     * @return object
     */
    public function existsIn($table, $column, $value)
    {
        $this->UserModel->table = $table;
        return $this->UserModel->select()
            ->where($column, $value)
            ->first();
    }

    /**
     * Get total with where 
     * clause from a specific table
     *
     * @param array $where
     * @param string $table
     * @return mixed
     */
    public function getTotal($where = null, $table = null)
    {
        if (!is_null($table)) {
            $this->UserModel->table = $table;
        } else {
            $this->UserModel->table = $this->user;
        }

        return $this->UserModel->getTotal($where);
    }

    /**
     * Get users with where 
     * clause from a specific table
     *
     * @param  array $where
     * @param string $table
     * @return object
     */
    public function getUsers($where = null, $table = null)
    {
        if (!is_null($table)) {
            $this->UserModel->table = $table;
        } else {
            $this->UserModel->table = $this->user;
        }

        return $this->UserModel->get($where);
    }

    /**
     * Get all users from a specific table
     *
     * @param string $table
     * @return object
     */
    public function getAllUsers($table = null)
    {
        if (!is_null($table)) {
            $this->UserModel->table = $table;
        } else {
            $this->UserModel->table = $this->user;
        }

        return $this->UserModel->findAll();
    }

    /**
     * Get a single User details
     *
     * @param array $where
     * @param string $table
     * @return object
     */
    public function userDetails($where, $table = null)
    {
        if (!is_null($table)) {
            $this->UserModel->table = $table;
        } else {
            $this->UserModel->table = $this->user;
        }
        
        return $this->UserModel->where($where)->first();
    }

    /**
     * Create User Details
     *
     * @param array|object $data
     * @param string $userTable
     * @return mixed
     */
    public function createUser($data, $userTable)
    {

        $this->UserModel->table = $userTable;

        $data = (array)$data;

        if ($userTable == $this->user) {
            $this->UserModel->table = $this->user;
        } else {
            $this->UserModel->table = $this->staff;
        }

        if ($userTable == $this->staff || $userTable == $this->admin) {
            $data = add_associative_array($data, 'created_by', session('user_id'));
        }

        if ($userTable == $this->user) {
            $data = add_associative_array($data, 'created_by', $data['user_id']);
        }

        $datetime = date('Y-m-d H:i:s', time());
        
        $data = add_associative_array($data, 'created_at', $datetime);

        return $this->UserModel->save($data);
    }

    /**
     * Edit User details
     *
     * @param array|object $data
     * @param string $userTable
     * @return mixed
     */
    public function editUser($data, $userTable)
    {

        $this->UserModel->table = $userTable;

        if ($userTable == $this->user || $userTable == $this->authConfig['default.group']) {
            $this->UserModel->table = $this->user;
        } else {
            $this->UserModel->table = $this->staff;
        }

        $where = null;

        if (isset($data['user_id'])) {
            $where = [
                'user_id' => $data['user_id']
            ];
        } else if (isset($data['username'])) {
            $where = [
                'username' => $data['username']
            ];
        }

        if ($userTable == $this->staff || $userTable == $this->admin) {
            $data = add_associative_array($data, 'updated_by', session('user_id'));
        }

        if ($userTable == $this->user) {
            $data = add_associative_array($data, 'updated_by', session('user_id'));
        }

        $data = add_associative_array($data, 'updated_at', datetime());

        return $this->UserModel->simpleUpdate($where, $data);
    }

    /**
     * 
     * Change user status
     * Changes a user's status 
     * 
     * @param string $userId User id to change
     * @param boolean $status
     * @return mixed
     */
    public function changeUserStatus($userId, $status = null)
    {
        $userData = [
            'user_id' => $userId,
            'status' => UserState::ACTIVE,
            'updated_at' => datetime(),
            'updated_by' => $userId
        ];

        if ($status !== null) {
            $userData = set_array($userData, 'status', $status);
        }

        $this->UserModel->table = $this->user;
        $query = $this->UserModel->selectWhere('user_id', ['user_id' => $userId]);

        // Check if user is an admin/staff
        $isAdmin = null;

        if (!$query) {
            $this->UserModel->table = $this->staff;
            $query = $this->UserModel->selectWhere('user_id', ['user_id' => $userId]);
            $isAdmin = true;
        }
        
        if ($isAdmin) {
            $this->UserModel->table = $this->staff;
            return $this->UserModel->simpleUpdate(['user_id' => $userId], $userData);
        }

        $this->UserModel->table = $this->user;
        return $this->UserModel->simpleUpdate(['user_id' => $userId], $userData);
    }

    /**
     * Create User Auth
     *
     * @param array|object $user
     * @param boolean $tempPassword
     * @param boolean $isStaff
     * @return boolean
     */
    public function createUserAuth($user, $tempPassword = false, $isStaff = false)
    {
        try {

            if ($tempPassword) {
                $user = (array)$user;
                $user['password'] = ($this->authConfig['temporal.password'] === 'random') ? unique_code(10) : $this->authConfig['temporal.password'];
                $this->temporalPassword = $user['password'];
            }

            $user = objectify($user);

            $userId = $this->auth->createUser($user->email, $user->password, $user->username, $user->user_id, $isStaff);
            $this->auth->addMember($user->user_id, $user->role);
            $verificationCode = $this->auth->getVerificationCode($userId);

            if (!$this->authConfig['auto.verification'] && !$this->authConfig['verification']) {
                return true;
            } else if (!$this->authConfig['auto.verification'] && $this->authConfig['verification']) {
                return true;
            } else if ($this->authConfig['auto.verification']) {

                $this->auth->autoVerifyUser($userId, $verificationCode);
                return true;
            }
        } catch (Exception $error) {
            log_message('error', $error->getMessage() . ' in ' . $error->getFile() . ' on line ' . $error->getLine());
            log_message('app', 'A User with Id: ' . $user->user_id . ' and Email: ' . $user->email . ' could not given an authentication detail');
            return false;
        }

        return false;
    }

    /**
     * Edit User's Auth Details
     *
     * @param object $user
     * @return bool
     */
    public function editUserAuth($user)
    {

        $exists = $this->auth->getUser($user->user_id);

        if ($exists) {
            return (bool) $this->auth->updateUser($user->user_id, $user->email, $user->password, $user->username);
        }

        return false;
    }

    /**
     * Check User's Current Password
     *
     * @param object $user
     * @return bool
     */
    public function checkCurrentPassword($user)
    {
        $valid = $this->auth->login($user->user_id, $user->current_password);

        if ($valid) {
            return true;
        }
        
        return false;

    }

    /**
     * Update User's Password
     *
     * @param object $user
     * @return bool
     */
    public function editUserPassword($user)
    {

        $valid = $this->checkCurrentPassword($user);

        if ($valid) {
            return (bool) $this->auth->updateUser($user->user_id, $user->email, $user->password, $user->username);
        }

        return false;
    }

    public function resetPassword($user)
    {

        $updated = $this->auth->updateUser(
            $user->user_id, 
            $user->email, 
            $user->password, 
            $user->username
        );

        if ($updated) {
            return true;
        }

        return false;
    }

    /**
     * Delete User Details
     *
     * @param string $userId
     * @param bool $softDelete
     * @return bool
     */
    public function deleteUser($userId, $softDelete = true)
    {

        $user = $this->existsIn($this->user, 'user_id', $userId);

        // Check if user is an admin/staff
        $isAdmin = null;

        if (!$user) {
            $user = $this->existsIn($this->staff, 'user_id', $userId);
            $isAdmin = true;
        }

        if (!$user) {
            return false;
        }

        if ($isAdmin && $softDelete) {

            $this->UserModel->table = $this->staff;

            $this->UserModel->softDelete(
                ['user_id' => $userId],
                [
                    'status' => UserState::BANNED,
                    'is_admin' => ZERO,
                    'deleted_at' => datetime(),
                    'deleted_by' => session('user_id'),
                    'is_deleted' => ONE
                ]
            );

            $this->auth->softDeleteUser($userId);

            return true;
        }

        if ($isAdmin && !$softDelete) {
            $this->UserModel->table = $this->staff;
            $this->UserModel->delete('user_id', $userId);
            $this->auth->deleteUser($userId);
            return true;
        }

        if ($softDelete) {

            $this->UserModel->table = $this->user;

            $this->UserModel->softDelete(
                ['user_id' => $userId],
                [
                    'status' => UserState::BANNED,
                    // 'is_admin' => ZERO,
                    'deleted_at' => datetime(),
                    'deleted_by' => session('user_id'),
                    'is_deleted' => ONE
                ]
            );

            $this->auth->softDeleteUser($userId);

            return true;
        }

        if (!$softDelete) {
            $this->UserModel->table = $this->user;
            $this->UserModel->delete('user_id', $userId);
            $this->auth->deleteUser($userId);
            return true;
        }

        return false;
    }
}
