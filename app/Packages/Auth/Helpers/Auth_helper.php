<?php 

use_library('Auth.Authy', null, 'auth');

if ( ! function_exists('authy'))
{
    /**
     * The auth library helper function  
     */
    function authy()
    {
        return app()->auth;
    }
}

if ( ! function_exists('auth'))
{
    /**
     * The auth library helper function 
     * An alias to function above 
     */
    function auth()
    {
        return app()->auth;
    }
}

if ( ! function_exists( 'auto_verify_user' ))
{   
    /**
    * Auto verify user  
    */
    function auto_verify_user(string $user_id)
    {
        // return ci()->aauth->createUser($email, $password, $username, $user_id, $is_admin);
    }
}

if ( ! function_exists( 'create_user_auth' ))
{
    /**
    * Create User authentication 
    */
    function create_user_auth($email, $password, $username = false, $user_id = false, $is_admin = false)
    {
        return auth()->createUser(
            $email, $password, $username, $user_id, $is_admin
        );
    }
}

if ( ! function_exists( 'update_user_auth' ))
{
    /**
    * Update User authentication 
    */
    function update_user_auth($user_id, $email = false, $password = false, $username = false)
    {
        return auth()->updateUser(
            $user_id, $email, $password, $username
        );
    }
}

if ( ! function_exists('authMailConfiguration'))
{
    /**
     * Auth Mail Configuration
     *
     * @param callable $configuration
     * @return mixed
     */
    function authMailConfiguration(callable $configuration)
    {
        return $configuration;
    }

}
