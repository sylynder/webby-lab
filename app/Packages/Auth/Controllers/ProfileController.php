<?php

use App\Middleware\WebMiddleware;

class ProfileController extends WebMiddleware
{
    public function __construct()
    {
        parent::__construct();
        $this->useDatabase();
        $this->checkAdminActive();

        use_service('Auth.UserService');
        use_helper('Auth.User');

    }

    public function index()
    {   
        $this->edit();
    }

    public function show()
    {
        $this->setTitle('List Users');
        return layout('Admin.layouts.form-table', 'Admin.profile.edit', $this->data);
    }

    public function edit()
    {
        $user_id = session('user_id');

        $user = $this->UserService->userDetails(['user_id' => $user_id], 'staff');

        if (!$user) {

            // route()->to('admin/dashboard')
            redirect('admin.dashboard');
        }

        $this->data['user'] = $user;

        $this->setTitle('Edit User');
        return layout('Admin.layouts.form-table', 'Admin.users-management.profile.edit', $this->data);
    }

}
