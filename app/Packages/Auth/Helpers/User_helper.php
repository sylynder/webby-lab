<?php 

if ( ! function_exists( 'user_details' )) 
{
    /**
     * User details
     *
     * @param string $user_id
     * @return mixed
     */
    function user_details($user_id = null, $from = 'users')
    {
        use_service('Auth/UserService');

        if ($user_id === null) {
            $user_id = session('user_id');
        }

        $where = ['user_id' => $user_id];
        $default_tables = app()->UserService->userConfig();
        $table = ($from) ? $from : $default_tables->user_table;
	    
        $user = app()->UserService->userDetails($where, $table);

        if ($user) {
            return $user; 
        }

        $table = $default_tables->staff_table;

        $user = app()->UserService->userDetails($where, $table);

        return ($user) ? $user : [];
	}
}

if ( ! function_exists( 'user_profile_image' )) 
{

    function user_profile_image($image = '')
    {
        use_config('Auth/User');

        $config = (object)config('user');

        if (empty($image)) {
            $image = $config->default_image_file;
        }

        $profile_image = file_exists($config->staff_profile_image . $image);

        if ( ! empty($profile_image)) {
            return load_path($config->staff_profile_image . $image);
        }

        $profile_image = file_exists($config->user_profile_image . $image);

        if ( ! empty($profile_image)) {
            return load_path($config->user_profile_image . $image);
        }

        return load_path($config->default_profile_image); 

    }
}

if ( ! function_exists( 'is_banned' )) 
{

    /**
     * Is User Banned
     *
     * @param string $user_id
     * @return boolean
     */
    function is_banned($user_id)
    {
        return auth()->isBanned($user_id);
    }
}

if ( ! function_exists( 'pre_verify_user' )) 
{
    /**
     * Preverify User
     *
     * @param string $user_id
     * @return mixed
     */
    function pre_verify_user($user_id)
    {
        $verification_code = authy()->getVerificationCode($user_id);

        if ($verification_code === null) {
            return false;
        }

        if (has_role('super_admin') || can('delete','user_management')) {
            return authy()->autoVerifyUser($user_id, $verification_code);
        }

        return false;
    }
}
