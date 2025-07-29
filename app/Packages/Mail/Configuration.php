<?php

namespace App\Packages\Mail;

class Configuration
{

    private $ci;

    public $default = 'env';

    public $config;

    public function __construct()
    {
        // get main ci object
        $this->ci =& get_instance();

        $this->ci->load->config('Mail/Config');
        $this->config = $this->ci->config->item('mail');
    }

    public function defaultConfig()
    {
        $config = $this->config['default'] ?: $this->default;

        return $this->{$config}();
    }

    public function env()
    {
        $config = [
            "smtp_debug"    => env('mailer.debug.mode'),
            "smtp_auth"     => env('smtp.auth'),   
            "smtp_host"     => env('smtp.host'),  
            "smtp_user"     => env('mail.server.user'), 
            "smtp_pass"     => env('mail.server.pass'),
            "smtp_security" => env('smtp.security'),
            "smtp_port"     => env('smtp.port')
        ];

        return $config;
    }

    public function mailtrap($config)
    {
        $config = [
            "smtp_debug"    => $config->debug,
            "smtp_auth"     => $config->auth,   
            "smtp_host"     => $config->host,  
            "smtp_user"     => $config->username, 
            "smtp_pass"     => $config->password,
            "smtp_security" => $config->security,
            "smtp_port"     => $config->port
        ];

        return $config;
    }

    // @TODO To be implemented
    public function database($config)
    {
        $config = is_object($config) ? $config : (object) $config;
        
        $config = [
            "smtp_debug"    => $config->debug,
            "smtp_auth"     => $config->auth,
            "smtp_host"     => $config->host,
            "smtp_user"     => $config->username,
            "smtp_pass"     => $config->password,
            "smtp_security" => $config->security,
            "smtp_port"     => $config->port
        ];

        return $config; 
    }
}
