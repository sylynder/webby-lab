<?php

if ( ! function_exists('roles')) 
{

    /**
     * Get all roles as an object | array
     * returns object as default 
     * returns array if to_array is true
     */
    function roles($to_array = false)
    {
        $roles = authy()->listRoles();

        if ($to_array) {
            return arrayfy($roles);
        }

        return $roles;
    }
}

if ( ! function_exists('role')) 
{

    /**
     * Get a role details
     */
    function role($role_id)
    {
        return authy()->getRole($role_id);
    }
}

if ( ! function_exists('all_roles')) 
{
    /**
     * Get all roles as an array
     */
    function all_roles()
    {
        return arrayfy(roles());
    }
}

if ( ! function_exists('hide_role')) 
{
    /**
     * Hide a role from all roles using it's name
     */
    function hide_role($roles, $role_name)
    {
        return array_filter($roles, function ($value) use ($role_name) {
            return $value['name'] != $role_name;
        });
    }
}

if ( ! function_exists('hide_roles')) 
{

    /**
     * Get all roles for admins as an array | object
     */
    function hide_roles(array $roles_to_hide, $to_array = false)
    {
        $roles = roles(true);

        foreach ($roles_to_hide as $role) {
            $roles = hide_role($roles, $role);
        }

        if ($to_array) {
            return json_decode(json_encode($roles), true);
        }

        return json_decode(json_encode($roles));
    }
}

if ( ! function_exists('add_role')) 
{
    /**
     * Add new role
     */
    function add_role($roleName, $tags = '', $description = '')
    {
        return authy()->createRole($roleName, $tags, $description);
    }
}

if ( ! function_exists('edit_role')) 
{
    /**
     * Edit existing role
     */
    function edit_role($role_id, $role_name = false, $tags = false, $description = false)
    {
        return authy()->updateRole($role_id, $role_name, $tags, $description);
    }
}

if ( ! function_exists('delete_role')) 
{

    /**
     * Edit existing role
     */
    function delete_role($role_id)
    {
        return authy()->deleteRole($role_id);
    }
}

if ( ! function_exists('user_roles')) 
{
    /**
     * Get all user's roles
     */
    function user_roles($user_id = false)
    {
        return authy()->getUserRoles($user_id);
    }
}

if (!function_exists('allow_role')) 
{
    /**
     * Add user to a role
     */
    function allow_role($user_id, $role_id)
    {
        return authy()->addMember($user_id, $role_id);
    }
}

if ( ! function_exists('assign_role')) 
{
    /**
     * alias of allow_role
     */
    function assign_role($user_id, $role_id)
    {
        return authy()->addMember($user_id, $role_id);
    }
}

if ( ! function_exists('deny_role')) 
{
    /**
     * Remove user from a role
     */
    function deny_role($user_id, $role_id)
    {
        return authy()->removeMember($user_id, $role_id);
    }
}

if ( ! function_exists('deny_user_roles')) 
{

    /**
     * Remove user from all roles
     */
    function deny_user_roles($user_id)
    {
        return authy()->removeMemberFromAll($user_id);
    }
}

if ( ! function_exists('has_role')) 
{
    /**
     * Check if user has a given role
     */
    function has_role($role_id, $user_id = false)
    {
        return authy()->isMember($role_id, $user_id);
    }
}

if ( ! function_exists('has_roles')) 
{
    /**
     * Check if user has a given roles e.g 'member|staff|admin'
     */
    function has_roles($roles, $user_id = false)
    {
        
        if (!contains('|', $roles)) {
            return false;
        }

        $roles = explode('|', $roles);
    
        $exists = [];

        foreach ($roles as $value) {
            $exists[] = has_role($value, $user_id);
        }

        return !in_array(false, $exists, true);

    }
}

if ( ! function_exists('is_admin')) 
{

    /**
     * Check if user is an admin
     */
    function is_admin($user_id = false)
    {
        return authy()->isAdmin($user_id);
    }
}

if ( ! function_exists('is_superadmin')) 
{
    /**
     * Check if user is a super admin
     */
    function is_superadmin($user_id = false)
    {
        return authy()->isSuperAdmin($user_id);
    }
}

if ( ! function_exists('check_super_admin')) 
{
    /**
     * Check if user is a superadmin 
     * Else redirect to given route
     */
    function check_superadmin($redirect_to)
    {
        if (!is_superadmin()) {
            error_message('Access Denied');
            redirect($redirect_to);
        }
    }
}

if ( ! function_exists('check_role')) 
{
    /**
     * Check if user has a said role 
     * Else redirect to given route
     */
    function check_role($role, $redirect_to)
    {
        if (!has_role($role)) {
            error_message('Access Denied');
            redirect($redirect_to);
        }
    }
}

if ( ! function_exists('role_name')) 
{
    /**
     * Get a role's name using it's id
     */
    function role_name($role_id)
    {
        return authy()->getRoleName($role_id);
    }
}

if ( ! function_exists('role_id')) 
{
    /**
     * Get a role's integer id using it's name
     */
    function role_id($role_id)
    {
        return authy()->getRoleID($role_id);
    }
}

if ( ! function_exists('sub_roles')) 
{
    /**
     * Get sub_roles of a given role
     */
    function sub_roles($role_id)
    {
        return authy()->getSubRoles($role_id);
    }
}

if ( ! function_exists('add_sub_role')) 
{
    /**
     * Add a sub_role under a role
     */
    function add_sub_role($role_id, $sub_role_id)
    {
        return authy()->addSubRole($role_id, $sub_role_id);
    }
}

if ( ! function_exists('delete_sub_role')) 
{
    /**
     * Delete a sub_role under a role
     */
    function delete_sub_role($role_id, $sub_role_id)
    {
        return authy()->removeSubRole($role_id, $sub_role_id);
    }
}
