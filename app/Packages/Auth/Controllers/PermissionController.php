<?php

use App\Middleware\AdminMiddleware;

class PermissionController extends AdminMiddleware
{

    public function __construct()
    {
        parent::__construct();
        $this->useDatabase();
        $this->checkAdminActive();

        use_service('Auth.UserService');
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $this->data['permissions'] = permissions();
        $this->setTitle('List Permissions');
        return layout('layouts/auth', 'users-management/permissions.lists', $this->data);
    }

    public function create()
    {
        $this->data['permissions'] = permissions();

        $this->data['permission_actions'] = (is_superadmin(session('user_id')))
            ? permission_actions()
            : hide_action(['assign', 'manage']);

        $this->setTitle('Create Permissions');
        return layout('layouts/auth', 'users-management/permissions.create', $this->data);
    }

    public function store()
    {
        $permission = clean(input()->post());
        $permission_name = slugify($permission['permission_name'], '_');
        $permission = add_associative_array($permission, 'permission_name', $permission_name);

        $this->validate->set_data($permission);

        validate('permission_name', 'permission', 'trim|required|is_unique[permissions.name]');
        validate('permission_actions', 'actions', 'trim|required', ['is_required' => 'Select at least one action']);
        validate('description', 'Description', 'trim|max_length[180]');

        if (!form_valid()) {
            $this->create();
        }

        check_role('super_admin', 'admin.permissions.create');

        if (form_valid()) {

            $permission = (object)$permission;

            $saved = add_permission($permission->permission_name, $permission->permission_actions, $permission->description);

            ($saved)
                ? route()->to('admin.permissions.create')->withSuccess('Permission Saved Successfully')
                : route()->to('admin.permissions.create')->withError('Sorry, Permission Was Not Save, Try Again');
        }
    }

    public function edit($roleId)
    {
        $roleId = clean($roleId);

        $this->data['permission'] = authy()->getRole($roleId);
        $this->data['permissions'] = permissions();
        $this->setTitle('Edit permission');

        return layout('layouts/auth', 'users-management/permissions.edit', $this->data);
    }

    // public function show($roleId)
    // {
    //     $roleId = clean($roleId);

    //     $permission = authy()->getRole($roleId);

    //     $this->data['permission'] = $permission;
    //     $this->data['permissions'] = permission_permissions($roleId);

    //     $this->setTitle('Create permission');
    //     return layout('layouts/auth', 'users-management/permissions.view', $this->data);
    // }

    public function update($roleId)
    {
        $roleId = clean($roleId);
        $permission = clean(input()->post());
        $permission_name = slugify($permission['permission_name'], '_');
        $permission = add_associative_array($permission, 'permission_name', $permission_name);
        $permission = add_associative_array($permission, 'id', $roleId);

        $this->validate->set_data($permission);

        validate('permission_name', 'permission', 'trim|required|is_unique[permissions.name,id,' . $roleId . ']');
        validate('permission_tags', 'Tags', 'trim|required');
        validate('description', 'Description', 'trim|max_length[180]');

        if (!form_valid()) {
            $this->edit($roleId);
        }

        if (form_valid()) {

            $permission = (object)$permission;

            $saved = edit_role($permission->id, $permission->permission_name, $permission->permission_tags, $permission->description);

            ($saved)
                ? route()->to('admin.permissions.edit.' . $roleId)->withSuccess('Permission Updated Successfully')
                : route()->to('admin.permissions.edit.' . $roleId)->withError('Sorry, Permission Was Not Updated, Try Again');
        }
    }

    public function delete($permissionId)
    {
        $permissionId = clean($permissionId);

        $deleted = false;

        if (!empty($roleId)) {
            $deleted = delete_permission($permissionId);
        }

        ($deleted)
            ? route()->to('admin.permissions.list')->withSuccess('Permission Has Been Removed Successfully')
            : route()->to('admin.permissions.list')->withError('Sorry, Permission Was Not Removed, Please Try Again');
    }

    public function getActions()
    {
        if (is_ajax_request()) {
            $permissionId = clean(input()->post('id'));

            $permission = authy()->getPermission($permissionId);
            
            // $actions = strtoarr(',', $permission->actions);
            // return $actions;
            return $this->json(['actions' => $permission->actions]);
        }

        if (empty(uri_segment(4))) {
            dd('off');
        }

        // $id = uri_segment(4);
        // $avaliableActions = authy()->getPermission($id)->actions;
        // $avaliableActions = strtoarr(',', $avaliableActions);

        // $permissionId = clean(input()->post('id'));

            // $permission = authy()->getPermission($permissionId);
            
            
// $permission = authy()->getPermission($id);
// dd($this->json(['actions' => $avaliableActions]));

        // arrtostr(',', $permissions_actions)
    }

    // public function assign($roleId)
    // {
    //     $roleId = clean($roleId);

    //     $permission = authy()->getRole($roleId);

    //     // if (!is_superadmin()) {
    //     //     route()->to('admin/dashboard')->withError('Sorry, Access Denied');
    //     // }

    //     $this->data['permission'] = $permission;
    //     $this->data['permissions'] = permissions();
    //     $this->data['permissions'] = permissions();
    //     $this->data['permission_permissions'] = permission_permissions($roleId);

    //     $this->data['permissions_actions'] = (is_superadmin(session('user_id')))
    //         ? permission_actions()
    //         : hide_action(['delete', 'manage']);

    //     $this->setTitle('Assign permission Permissions');
    //     return layout('layouts/auth', 'users-management/permissions.assign', $this->data);
    // }


    // public function edit_assign($roleId)
    // {
    //     $roleId = clean($roleId);
    //     $this->assign($roleId);
    // }

    // public function assign_role($roleId)
    // {
    //     $roleId = clean($roleId);
    //     $permission = clean(input()->post());
    //     $permission = add_associative_array($permission, 'permission_id', $roleId);

    //     $this->form_validation->set_data($permission);

    //     validate('permission_id', 'permission', 'required|trim');
    //     validate('permission', 'Permission', 'required|trim');
    //     validate('permission_actions', 'Actions', 'required|trim', ['required' => 'At least one action should be set']);

    //     if (!form_valid()) {
    //         $this->assign($roleId);
    //     }

    //     if (form_valid()) {
    //         $permission = (object)$permission;

    //         // first check if permission exist already
    //         // then revoke first and reassign
    //         $exists = permission_has_permission($permission->permission, $permission->permission_id);

    //         if ($exists) {
    //             deny_permission_permission($permission->permission_id, $permission->permission);
    //         }

    //         $saved = assign_permission_permission(
    //             $permission->permission_id,
    //             $permission->permission,
    //             $permission->permission_actions
    //         );

    //         ($saved)
    //             ? route()->to('admin.permissions.assign-permission.' . $roleId)->withSuccess('permission Assigned Permission Successfully')
    //             : route()->to('admin.permissions.assign-permission.' . $roleId)->withError('Sorry, Permission Was Not Assigned, Try Again');
    //     }
    // }

    // public function revoke($roleId, $permissionId)
    // {
    //     $roleId = clean($roleId);
    //     $permissionId = clean($permissionId);

    //     $revoked = false;

    //     if (!empty($roleId) && !empty($roleId)) {
    //         $revoked = deny_permission_permission($roleId, $permissionId);
    //     }

    //     ($revoked)
    //         ? route()->to('admin.permissions.assign-permission.' . $roleId)->withSuccess('permission Permission Has Been Revoked Successfully')
    //         : route()->to('admin.permissions.assign-permission.' . $roleId)->withError('Sorry, permission Permission Was Not Revoked, Please Try Again');
    // }
}
