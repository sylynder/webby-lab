<?php

/**
 * Password Class
 *
 * Provides a central location to handle password
 * related tasks like hashing, verifying, validating, etc.
 */
class Password
{

    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Hash password
     * 
     * Hash the password for storage in the db
     * 
     * @param string $pass Password to hash
     * @param $userid
     * @return string Hashed password
     */
    public function hash(string $password, string $userId)
    {
        $hashOptions = [];

        if (
            (defined('PASSWORD_ARGON2I') && $this->config['password.hash.algo'] === PASSWORD_ARGON2I)
            || (defined('PASSWORD_ARGON2ID') && $this->config['password.hash.algo'] === PASSWORD_ARGON2ID)
        ) {
            $hashOptions = [
                'memory_cost' => $this->config['password.memory.cost'],
                'time_cost'   => $this->config['password.time.cost'],
                'threads'     => $this->config['password.threads'],
            ];

            $this->config['password.hash.algon2'] = true;

        } else {
            $hashOptions = [
                'cost' => $this->config['password.default.cost'],
            ];
        }

        $this->config['password.hash.options'] = $hashOptions;
        
        if ($this->config['use.password.hash']) {
            return password_hash($password, $this->config['password.hash.algo'], $this->config['password.hash.options']);
        }

        $salt = sha1($userId);

        return hash($this->config['hash'], $salt . $password);
    }

    /**
     * Verifies a password against a previously hashed password.
     *
     * @param string $password The password we're checking
     * @param string $hash     The previously hashed password
     */
    public function verify(string $password, string $hash): bool
    {
        if ($this->config['use.password.hash']) {
            return password_verify($password, $hash);
        }

        return ($password == $hash ? true : false);
    }

    /**
     * Checks to see if a password should be rehashed.
     *
     * @param string $hashedPassword
     * @return boolean
     */
    public function needsRehash(string $hashedPassword): bool
    {
        return password_needs_rehash($hashedPassword, $this->config['password.hash.algo']);
    }

}
