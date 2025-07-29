<?php

/*
| -------------------------------------------------------------------------
| Your Authentication Configurations
| -------------------------------------------------------------------------
| This file lets you define authentication settings to simplify and 
| enable authentication and authorization in your application. 
| You can configure things like:  tables, database_profile etc
|
*/

$authy = [];

$authy["default"] = [

    // ----------------------- User Management Features ----------------------

    'no.permission'                  => false,

    // Default User Groups
    'super.admin.group'              => 'super_admin',
    'admin.group'                    => 'staff',
    'default.group'                  => 'user',
    'public.group'                   => 'guest',

    // Database profile to use
    'db.profile'                     => 'authy_db',

    // Tables for authentication system
    'users'                          => 'user_auth',
    'admins'                         => 'staff_auth',
    'social'                         => 'social_auth',
    'groups'                         => 'roles',
    'role.groups'                    => 'role_groups',
    'user.groups'                    => 'user_roles',
    'permissions'                    => 'permissions',
    'group.permissions'              => 'role_permissions',
    'user.permissions'               => 'user_permissions',
    'pms'                            => 'local_inbox',
    'user.variables'                 => 'user_metadata',
    'user.tokens'                    => 'user_tokens',
    'login.attempts'                 => 'login_attempts',

    // ----------------------- Security check --------------------------------

    'use.tokens'                     => false,
    'use.sessions'                   => true,
    'use.superactions'               => false,
    
    // Misc
    'remember'                       => ' +3 days',
    'additional.valid.chars'         => ['.', '@', '_', '-'],

    // Brute force
    'ddos.protection'                => true,

    // Recaptcha
    'recaptcha.active'               => false,
    'recaptcha.login.attempts'       => 4,
    'recaptcha.site.key'             => '',
    'recaptcha.secret'               => '',

    // TOTP
    'totp.active'                    => false,
    'totp.only.on.ip.change'         => false,
    'totp.reset.over.reset.password' => false,
    'totp.two.step.login.active'     => false,
    'totp.two.step.login.redirect'   => '/auth/twofactor-verification/',

    // Login
    'max.login.attempt'              => 6,
    'max.login.attempt.time.period'  => "10 minutes",
    'remove.successful.attempts'     => true,

    'auto.login'                     => true,
    'set.user.id.field'              => true,
    'login.with.user.id'             => true,
    'login.with.username'            => false,
    'verify.with.pin.code'           => false,


    // Password
    'hash'                           => 'sha384',
    'default.password'               => '',
    'use.password.hash'              => true,
    'password.hash.algo'             => PASSWORD_BCRYPT, // You can replace with PASSWORD_BCRYPT|PASSWORD_ARGON2ID|PASSWORD_ARGON2I
    'password.hash.algon2'           => false,
    'password.default.cost'          => 11,
    'password.memory.cost'           => 2048,
    'password.time.cost'             => 4,
    'password.threads'               => 4,
    'password.hash.options'          => [],
    

    // password min and max length
    'max'                            => 18,
    'min'                            => 6,

    // Authentication email settings configurations
    'temporal.password'              => 'random', //'@temppassword', // best to implement a random string
    'default.firstname'              => 'User', // used as firstname when sending emails to unknown users
    'app.email'                      => '',
    'app.name'                       => env('app.name'),
    'from.name'                      => 'Administrator',
    'support.email'                  => env('support.email'),
    'support.team'                   => env('support.team'),

    'activation.email.view'          => 'Auth/emails/activate-account',
    'activation.success.view'        => 'Auth/emails/account-activated',
    'reset.password.view'            => 'Auth/emails/reset-password',
    'reset.password.success.view'    => 'Auth/emails/reset-success',

    'use.email'                      => '',
    'email.service'                  => 'PHPMailer', // PHPMailer Or CIMailer
    'email.config'                   => false, // false or array configuration for CIMailer

    // Verification Settings
    'verification'                   => true,
    'verification.code.length'       => 6,
    'auto.verification'              => true,

    // time | Read about the time travel function in webby
    // always should be in this format [Y-m-d H:i:s]
    'verification.expire.at'         => travel()->to("30 days")->format(), 
    
    // Links
    'reset.user.password.link'       => '/auth/reset-user-password/',
    'reset.admin.password.link'      => '/auth/reset-admin-password/',
    'verification.link'              => '/auth/verification/',
    'verification.success.link'      => '/auth/verification-success/',
    'verification.magic.link'        => '/auth/magic-link/',


    // ----------------------- Messaging Feature -----------------------------

    // Private Messaging Settings
    'pm.active'                      => true,
    'pm.encryption'                  => false,
    'pm.cleanup.max.age'             => "3 months",
];

$config['auth'] = $authy['default'];
