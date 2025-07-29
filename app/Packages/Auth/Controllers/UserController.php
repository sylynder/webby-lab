<?php

use App\Middleware\AdminMiddleware;

class UserController extends AdminMiddleware
{
    public $source = 'staff';

    public function __construct()
    {
        parent::__construct();
        $this->useDatabase();
        $this->checkAdminActive();

        use_service('Auth.UserService');
        use_helper('Auth.User');
        use_form('Users.UserForms');

    }

    public function index($user_id)
    {
        $this->show($user_id);
    }

    public function show($user_id)
    {
        $user_id = clean($user_id);

        $user = $this->UserService->userDetails(['user_id' => $user_id], 'staff');
        
        if (!$user) {
            
            route()->to('admin/users/list')
                   ->withError('User Does Not Exist');
        }

        $this->data['user'] = $user;

        $this->setTitle('View User');

        return layout('layouts/auth', 'users-management/users/show', $this->data);
    
    }

    public function list()
    {
        
        $staff = $this->UserService->getUsers([
            'is_deleted' => 0,
        ], 'staff');

        $this->data['users'] = $staff;

        $this->setTitle('List Users');
        return layout('layouts.auth', 'users-management.users.list', $this->data);
    }

    public function create()
    {
        $this->setTitle('Create User');
        return layout('layouts.auth', 'users-management.users.create', $this->data);
    }

    public function store()
    {
        $user = clean(input()->post());

        $image = files('user_image');

        $this->validate->set_data($user);

        if (!UserForms::save()) {
            $this->create();
        }

        if (UserForms::save()) {

            $user_id = $this->UserService->generateUuid();
            $user = (object)$user;

            $user = [
                'user_id' => $user_id,
                'username' => $user->username,
                'email' => $user->email,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'phone_number' => $user->phone_number,
                'gender' => $user->gender,
                'role' => $user->role,
                'about' => $user->about
            ];

            $image = has_file('user_image') ? $image  : '';

            $saved = $this->UserService->createUser($user, $this->source);
            $created = $this->UserService->createUserAuth($user, true, true);

            if ($created && $saved) {
                
                $this->UserService->uploadProfileImage($image, $user_id, true);
                success_message('User Created Successfully');
                redirect('admin.users.list');
            }

            if (!$created ||!$saved) {
                error_message('Sorry User Was Not Created');
                redirect('admin.users.list');
            }
        }
    }

    public function edit($user_id)
    {
        $user_id = clean($user_id);

        $user = $this->UserService->userDetails(['user_id' => $user_id], $this->source);

        if (!$user) {
            route()->to('admin/users/list')
                ->withError('User Does Not Exist');
        }

        $this->data['user'] = $user;

        $this->setTitle('Edit User');
        return layout('layouts.auth', 'users-management.users.edit', $this->data);
    }

    public function update($user_id)
    {
        $user_id = clean($user_id);

        $user = clean(input()->post());
        $user = set_array($user, 'user_id', $user_id);

        $image = files('user_image');

        $this->validate->set_data($user);

        $user = (object)$user;

        $user = [
            'user_id' => $user_id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'phone_number' => $user->phone_number,
            'gender' => $user->gender,
            'role' => $user->role,
            'about' => $user->about
        ];

        if (!UserForms::update($user_id)) {
            $this->edit($user_id);
        }

        if (UserForms::update($user_id)) {

            $updated = $this->UserService->editUser($user, $this->source);

            if (has_file('user_image')) {
                $this->UserService->uploadProfileImage($image, $user_id, true);
            }

            return ($updated)
                ? route()->to('admin.users.list')->withSuccess('User Details Updated Successfully')
                : route()->to('admin.users.list')->withError('Sorry User Details Was Not Updated');
        }
    }

    // Reset Password View
    public function resetUserPassword($user_id)
    {
        $user_id = clean($user_id);

        $user = $this->UserService->userDetails(['user_id' => $user_id], $this->source);

        if (!$user) {
            route()->to('admin/users/list')
                ->withError('User Does Not Exist');
        }

        $this->data['user'] = $user;

        $this->setTitle('Edit User');
        return layout('layouts.auth', 'users-management.users.reset-password', $this->data);
    }

    // Change user password
    // This for admin (hard) password reset
    public function changeUserPassword($user_id)
    {
        $user = clean(input()->post());
        $user = set_array($user, 'user_id', $user_id);

        $this->validate->set_data($user);

        validate('new_password', 'New Password', 'trim|required|min_length[8]');
        validate('verify_password', 'Verify Password', 'trim|required|matches[new_password]|min_length[8]');
        
        $user = (object)$user;

        $user = (object)[
            'user_id' => $user->user_id,
            'email' => false,
            'username' => false,
            'password' => $user->new_password
        ];

        $valid = form_valid();

        if (!$valid) {
            $this->resetUserPassword($user_id);
        }

        if ($valid) {

            $updated = $this->UserService->resetPassword($user);

            return ($updated)
                ? route()->to('admin.users.show', $user_id)->withSuccess('Password Reset Successfully')
                : route()->to('admin.users.show', $user_id)->withError('Sorry Password Reset Failed');
        }

    }

    public function activateUser($userId)
    {

        $userId = clean($userId);

        $member = $this->UserService->model($this->source)
            ->where(['user_id' => $userId])
            ->first();

        if (!$member) {
            route()->to('admin/users/list')
                ->withError('User Does Not Exist');
        }

        use_helper('Common/Common');

        $activated = false;

        if ($member->status == \App\Enums\Status::SET) {

            $this->UserService
                ->model($this->source)
                ->simpleUpdate(['user_id' => $member->user_id], [
                    'status' => \App\Enums\Status::ACTIVE
                ]);

            $activated = true;
        }

        return ($activated)
                ? route()->to('admin.users.list')->withSuccess('User Activated Successfully')
                : route()->to('admin.users.list')->withError('User Not Activated');
        
    }

    public function delete($userId)
    {
        $userId = clean($userId);

        $user = $this->UserService->existsIn($this->source, 'user_id', $userId);

        if (!$user) {
            route()->to('admin/users/list')
                ->withError('User Does Not Exist');
        }

        $deleted = $this->UserService->deleteUser($user->user_id, true);

        return ($deleted)
            ? route()->to('admin.users.list')->withSuccess('User Was Deleted Successfully')
            : route()->to('admin.users.list')->withError('User Could Not Be Deleted');

    }

}
