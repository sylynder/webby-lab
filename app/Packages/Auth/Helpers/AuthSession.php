<?php

/**
 * This helper checks authentication 
 * availability so as to allow access to
 * secured routes
 * 
 * @author Oteng Kwame Appiah-Nti
 */

namespace App\Packages\Auth\Helpers;

class AuthSession
{

    /**
     * Checks if user's session is active
     * And a member of the user authentication group
     *
     * @return bool
     */
    public static function userActive()
    {
        if ((session('loggedin') === true && session('user_type') === 'user')
            || (session('loggedin') === true && session('user_type') === 'member')
            && (session('client_session') === true)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if admin's or staff's session is active
     * And a member of the admin authentication group
     *
     * @return bool
     */
    public static function adminActive()
    {
        if ((session('loggedin') === true && session('user_type') === 'admin')
            || (session('loggedin') === true && session('user_type') === 'staff')
            || (session('loggedin') === true && session('user_type') === 'super_admin')
            && (session('admin_session') === true)
        ) {
            return true;
        }

        return false;
    }
}
