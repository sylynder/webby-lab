<?php

defined('BASEPATH') or exit('No direct script access allowed');

if ( ! function_exists('permission_actions')) 
{
    /**
     * Get permission actions
     */
    function permission_actions()
    {
        return ['manage', 'assign', 'create', 'edit', 'view', 'read', 'delete', 'print'];
    }
}

if ( ! function_exists('hide_action')) 
{
    /**
     * Hide some actions
     *
     * @param string|array $action
     * @return array
     */
    function hide_action($action)
    {
        $actions = permission_actions(true);

        if (!is_array($action)) {
            $action = (array)$action;
        }

        return array_diff($actions, $action);
    }
}

if ( ! function_exists('action_exists')) 
{
    /**
     * Check if permission action exists
     * For css beautification
     */
    function action_exists(...$params)
    {
        [$action, $permission_actions, $true_state, $false_state] = $params;

        return contains($action, $permission_actions) ? $true_state : $false_state;
    }
}

if ( ! function_exists('all_permissions')) 
{
    /**
     * Get all permissions
     */
    function all_permissions()
    {
        return authy()->listPermissions();
    }
}

if ( ! function_exists('permissions')) 
{
    /**
     * Get all permissions as an object | array
     * returns object as default
     * returns array if to_array is true
     */
    function permissions($to_array = false)
    {

        $permissions = authy()->listPermissions();

        if ($to_array) {
            arrayfy($permissions);
        }

        return $permissions;
    }
}

if ( ! function_exists('permission')) 
{
    /**
     * Get permission using a given id or name
     */
    function permission($permission_id)
    {
        return authy()->getPermission($permission_id);
    }
}

if ( ! function_exists('permission_id')) 
{
    /**
     * Get permission id using a given name
     */
    function permission_id($permission_name)
    {
        return permission($permission_name)->id;
    }
}

if ( ! function_exists('permission_name')) 
{
    /**
     * Get permission name using a given id
     */
    function permission_name($permission_id)
    {
        return permission($permission_id)->name;
    }
}

if ( ! function_exists('add_permission'))
{
    /**
     * Add new permission 
     */
    function add_permission($permission_name, $actions = '', $description = '')
    {
        return authy()->createPermission($permission_name, $actions, $description);
    }
}

if ( ! function_exists('edit_permission'))
{
    /**
     * Edit permission using name
     */
    function edit_permission($permission_id, $permission_name = false, $description = false)
    {
        return authy()->updatePermission($permission_id, $permission_name, $description);
    }
}

if ( ! function_exists('delete_permission'))
{
    /**
     * Delete permission using permission_id
     */
    function delete_permission($permission_id)
    {
        return authy()->deletePermission($permission_id);
    }
}

if ( ! function_exists('user_permissions')) 
{
    /**
     * Get user's permissions using user_id
     */
    function user_permissions($user_id = false)
    {
        return authy()->getUserPermissions($user_id);
    }
}

if ( ! function_exists('role_permissions')) 
{
    /**
     * Get a given role's permissions using role_id
     */
    function role_permissions($role_id)
    {
        return authy()->listRolePermissions($role_id);
    }
}

if ( ! function_exists('has_permission')) 
{
    /**
     * Check if a user has a permission using permission name/id 
     * and user_id 
     */
    function has_permission($permission, $user_id = false)
    {
        return authy()->isAllowed($permission, $user_id);
    }
}

if ( ! function_exists('has_action')) 
{
    /**
     * Gets a users actions using permission name/id 
     * and user_id 
     */
    function has_action($action, $permission, $user_id = false)
    {
        return authy()->isActionAllowed($action, $permission, $user_id);
    }
}

if ( ! function_exists('role_action'))
{
    /**
     * Gets a role actions using permission name/id 
     * and role_id 
     */
    function role_action($action, $permission, $role_id = false)
    {
        return authy()->isRoleActionAllowed($action, $permission, $role_id);
    }
}

if ( ! function_exists('can')) 
{
    /**
     * Check if a user has or can perform 
     * a particular permission name/id and user_id 
     */
    function can($action, $permission, $user_id = false)
    {
        return has_action($action, $permission, $user_id);
    }
}

if ( ! function_exists('can_role')) 
{
    /**
     * Check if a role has or can perform 
     * a particular action with role, action and permission 
     */
    function can_role($role, $action, $permission)
    {
        return authy()->isRoleActionAllowed($action, $permission, $role);
    }
}

if ( ! function_exists('role_has_permission')) 
{
    /**
     * Check if a role has a permission using permission name/id 
     * and role_id 
     */
    function role_has_permission($permission, $role_id = false)
    {
        return authy()->isRoleAllowed($permission, $role_id);
    }
}

if ( ! function_exists('assign_permission'))
{
    /**
     * Give user a permission using permission name/id 
     * and user_id 
     */
    function assign_permission($user_id, $permission, $actions = null)
    {
        return authy()->allowUser($user_id, $permission, $actions);
    }
}

if ( ! function_exists('deny_permission'))
{
    /**
     * Deny user a permission using permission name/id 
     * and user_id 
     */
    function deny_permission($user_id, $permission)
    {
        return authy()->denyUser($user_id, $permission);
    }
}

if ( ! function_exists('assign_role_permission'))
{
    /**
     * Assign a role a permission using permission name/id 
     * and role_id 
     */
    function assign_role_permission($role_id, $permission, $actions = null)
    {
        return authy()->allowRole($role_id, $permission, $actions);
    }
}

if ( ! function_exists('deny_role_permission'))
{
    /**
     * Assign a role a permission using permission name/id 
     * and role_id 
     */
    function deny_role_permission($role_id, $permission)
    {
        return authy()->denyRole($role_id, $permission);
    }
}
