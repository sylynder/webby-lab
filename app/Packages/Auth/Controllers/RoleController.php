<?php

use App\Packages\Auth\Enums\UserState;
use App\Packages\Auth\Middleware\AdminMiddleware;

class RoleController extends AdminMiddleware
{

    public function __construct()
    {
        parent::__construct();
        $this->useDatabase();
        $this->checkAdminActive();

        // use_helper('Admin.Util');
        use_service('Auth.UserService');

        $this->data['default_roles'] = ['staff', 'super_admin', 'guest', 'member'];
        
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $this->data['roles'] = roles();
        $this->setTitle('List Roles');
        return layout('layouts/auth', 'users-management/roles.lists', $this->data);
    }

    public function create()
    {
        $this->data['roles'] = roles();
        // $this->data['role_names'] = arrayz(roles())->pick('name')->get();
        
        $this->setTitle('Create Role');
        return layout('layouts/auth', 'users-management/roles.create', $this->data);
    }

    public function store()
    {
        $role = clean(input()->post());
        $role_name = slugify($role['role_name'], '_');
        $role = add_associative_array($role, 'role_name', $role_name);

        $this->validate->set_data($role);

        validate('role_name', 'Role', 'trim|required|is_unique[roles.name]');
        validate('role_tags', 'Tags', 'trim|required');
        validate('description', 'Description', 'trim|max_length[180]');

        if (!form_valid()) {
            $this->create();
        }

        check_role('super_admin', 'admin.roles.create');

        if (form_valid()) {

            $role = (object)$role;

            $saved = add_role($role->role_name, $role->role_tags, $role->description);

            ($saved)
                ? route()->to('admin.roles.create')->withSuccess('Role Saved Successfully')
                : route()->to('admin.roles.create')->withError('Sorry, Role Was Not Save, Try Again');
        }
    }

    public function edit($roleId)
    {
        $roleId = clean($roleId);

        $this->data['role'] = authy()->getRole($roleId);
        $this->data['roles'] = roles();
        $this->setTitle('Edit Role');

        return layout('layouts/auth', 'users-management/roles.edit', $this->data);
    }

    public function show($roleId)
    {
        $roleId = clean($roleId);

        $role = authy()->getRole($roleId);

        $this->data['role'] = $role;
        $this->data['permissions'] = role_permissions($roleId);
// dd($this->data);
        $this->setTitle('Create Role');
        return layout('layouts/auth', 'users-management/roles.view', $this->data);
    }

    public function update($roleId)
    {
        $roleId = clean($roleId);
        $role = clean(input()->post());
        $role_name = slugify($role['role_name'], '_');

        $role = add_associative_array($role, 'role_name', $role_name);

        if (!($roleId >= 4)) {
            $role_name = role_name($roleId);
            $role = add_associative_array($role, 'role_name', $role_name);
        }
        
        $role = add_associative_array($role, 'id', $roleId);

        $this->validate->set_data($role);

        validate('role_name', 'Role', 'trim|required|is_unique[roles.name,id,' . $roleId . ']');
        validate('role_tags', 'Tags', 'trim|required');
        validate('description', 'Description', 'trim|max_length[180]');

        if (!form_valid()) {
            $this->edit($roleId);
        }

        if (form_valid()) {

            $role = (object)$role;

            $saved = edit_role($role->id, $role->role_name, $role->role_tags, $role->description);

            ($saved)
                ? route()->to('admin.roles.edit.' . $roleId)->withSuccess('Role Updated Successfully')
                : route()->to('admin.roles.edit.' . $roleId)->withError('Sorry, Role Was Not Updated, Try Again');
        }
    }

    public function assign($roleId)
    {
        $roleId = clean($roleId);

        $role = authy()->getRole($roleId);

        if (!is_superadmin()) {
            route()->to('admin/dashboard')->withError('Sorry, Access Denied');
        }

        $this->data['role'] = $role;
        $this->data['roles'] = roles();
        $this->data['permissions'] = permissions();
        $this->data['role_permissions'] = role_permissions($roleId);

        $this->data['permissions_actions'] = (is_superadmin(session('user_id')))
            ? permission_actions()
            : hide_action(['delete', 'manage']);

        $this->setTitle('Assign Role Permissions');
        return layout('layouts/auth', 'users-management/roles.assign', $this->data);
    }

    // public function edit_assign($roleId)
    // {
    //     $roleId = clean($roleId);
    //     $this->assign($roleId);
    // }

    public function assign_role($roleId)
    {
        $roleId = clean($roleId);
        $permission = clean(input()->post());
        $permission = add_associative_array($permission, 'role_id', $roleId);

        $this->form_validation->set_data($permission);

        validate('role_id', 'Role', 'required|trim');
        validate('permission', 'Permission', 'required|trim');
        validate('permission_actions', 'Actions', 'required|trim', ['required' => 'At least one action should be set']);

        if (!form_valid()) {
            $this->assign($roleId);
        }

        if (form_valid()) {
            $permission = (object)$permission;

            // first check if permission exist already
            // then revoke first and reassign
            $exists = role_has_permission($permission->permission, $permission->role_id);

            if ($exists) {
                deny_role_permission($permission->role_id, $permission->permission);
            }

            $saved = assign_role_permission(
                $permission->role_id,
                $permission->permission,
                $permission->permission_actions
            );

            ($saved)
                ? route()->to('admin.roles.assign-permission.' . $roleId)->withSuccess('Role Assigned Permission Successfully')
                : route()->to('admin.roles.assign-permission.' . $roleId)->withError('Sorry, Permission Was Not Assigned, Try Again');
        }
    }

    public function revoke($roleId, $permissionId)
    {
        $roleId = clean($roleId);
        $permissionId = clean($permissionId);

        $revoked = false;

        if (!empty($roleId) && !empty($permissionId)) {
            $revoked = deny_role_permission($roleId, $permissionId);
        }

        ($revoked)
            ? route()->to('admin.roles.assign-permission.' . $roleId)->withSuccess('Role Permission Has Been Revoked Successfully')
            : route()->to('admin.roles.assign-permission.' . $roleId)->withError('Sorry, Role Permission Was Not Revoked, Please Try Again');
    }

    public function delete($roleId)
    {
        $roleId = clean($roleId);

        $deleted = false;

        if (!empty($roleId)) {
            $deleted = delete_role($roleId);
        }

        ($deleted)
            ? route()->to('admin.roles.list')->withSuccess('Role Permission Has Been Removed Successfully')
            : route()->to('admin.roles.list')->withError('Sorry, Role Permission Was Not Removed, Please Try Again');
    }

    public function memberRoles($userId)
    {
        
        $userId = clean($userId);

        // $roles =  authy()->listRoles();//authy()->getUserRoles($userId);
        $user = $this->UserService->userDetails(['user_id' => $userId], 'users');

        if (!$user) {
            route()->to('admin/members/list')->withError('User Does Not Exist');
        }

        if ($user->status === UserState::SET) {
            route()->to('admin/members/list')->withError('User Account Not Activated');
        }

        if (is_superadmin() === false) {
            route()->to('admin/dashboard')->withError('Sorry, Access Denied');
        }
        
        $this->data['user'] = $user;
        
        $this->data['user_roles'] = user_roles($userId);
        // dd(role_name(role_id($user->role)));
        $roles = (is_superadmin(session('user_id')))
            ? roles() 
            : hide_roles(['super_admin']);

        if ($user->role === role_name(role_id($user->role))) {
            $roles = hide_roles(['super_admin']);
        }

        if ($user->role !== role_name(role_id($user->role))) {
            $roles = hide_roles(['super_admin']);
        }

        // dd((array) $roles, hide_roles(['super_admin']));
        
        $this->data['roles'] = (array) $roles;

        $this->setTitle('List Member Roles');
        return layout('layouts/auth', 'users-management/users/roles', $this->data);
    }

    public function userRoles($userId)
    {
        
        $userId = clean($userId);

        // $roles =  authy()->listRoles();//authy()->getUserRoles($userId);

        $user = $this->UserService->userDetails(['user_id' => $userId], 'staff');

        if (!$user) {
            route()->to('admin/users/list')->withError('User Does Not Exist');
        }
        
        if (!is_superadmin()) {
            route()->to('admin/dashboard')->withError('Sorry, Access Denied');
        }
        
        $this->data['user'] = $user;
        
        $this->data['user_roles'] = user_roles($userId);
        
        $this->data['roles'] = (is_superadmin(session('user_id')))
            ? roles()
            : hide_roles(['super_admin']);

        $this->setTitle('List User Roles');
        return layout('layouts/auth', 'users-management/users/roles', $this->data);
    }

    public function addRole($userId)
    {

        $referer = server('HTTP_REFERER');
        
        $userId = clean($userId);
        $role = clean(post());

        $role = set_array($role, 'user_id', $userId);

        $this->validate->formData($role);

        validate('role_id', 'Role', 'required|trim');
        validate('user_id', 'User Id', 'required|trim', ['required' => 'User Id needed']);
        
        $member = (contains('users/member-roles', $referer)) ? 'users/member-roles' : null;
        $staff = (contains('staff/staff-roles', $referer)) ? 'staff/staff-roles' : null;
        $user = (contains('users/user-roles', $referer)) ? 'users/user-roles' : null;
        
        $route = $staff ?? $user ?? $member;

        if (!form_valid()) {
            route()->to('admin/'. $route, $userId)->withError('Please select role');
            exit;
        }

        if (form_valid()) {

            $role = (object) $role;

            // first check if role exist already
            // then revoke first and reassign
            $exists = has_role($role->role_id, $role->user_id);

            if ($exists && !is_superadmin($role->user_id)) {
                deny_role($role->user_id, $role->role_id);
            }

            // if ($exists && $role->role_id !== '1') {
            //     deny_role($role->user_id, $role->role_id);
            // }

            $saved = assign_role(
                $role->user_id,
                $role->role_id
            );

            ($saved)
                ? route()->to('admin/' . $route, $userId)->withSuccess('User Assigned Role Successfully')
                : route()->to('admin/' . $route, $userId)->withError('Sorry, Role Was Not Assigned, Try Again');
        }

    }

    public function revokeRole($roleId, $userId)
    {
        $userId = clean($userId);
        $roleId = clean($roleId);

        $referer = server('HTTP_REFERER');
        $revoked = false;

        $member = (contains('users/member-roles', $referer)) ? 'users/member-roles' : null;
        $staff = (contains('staff/staff-roles', $referer)) ? 'staff/staff-roles' : null;
        $user = (contains('users/user-roles', $referer)) ? 'users/user-roles' : null;
        
        $route = $staff ?? $user ?? $member;

        if ( (!empty($userId) 
            && !empty($roleId) 
            && $roleId !== '1')
        ) {
            $revoked = deny_role($userId, $roleId);
        }

        if ($roleId === '1' && has_role('staff', $userId)) {
            $revoked = deny_role($userId, $roleId);
        }

        ($revoked)
            ? route()->to('admin/' . $route, $userId)->withSuccess('User Role Has Been Revoked Successfully')
            : route()->to('admin/' . $route, $userId)->withError('Sorry, User Role Was Not Revoked, Please Try Again');
    }

}
