<?php

/**
*   AuthorizationToken
* -------------------------------------------------------------------
* API Token Check and Generate
*
*/

use \Firebase\JWT\JWT;

class AuthToken 
{
    /**
     * App variable
     *
     * @var
     */
    private $app;

    /**
     * Token Key
     */
    protected $tokenKey;

    /**
     * Token algorithm
     */
    protected $tokenAlgorithm;

    /**
     * Request Header Name
     */
    protected $tokenHeader = ['authorization','Authorization'];

    /**
     * Token Expire Time
     * ----------------------
     * ( 1 Day ) : 60 * 60 * 24 = 86400
     * ( 1 Hour ) : 60 * 60     = 3600
     */
    protected $tokenExpireTime = 86400; 


    public function __construct()
	{
        $this->app = ci();

        /** 
         * jwt config file load
         */
        $this->app->load->config('Auth/JWT');

        /**
         * Load Config Items Values 
         */
        $this->tokenKey        = $this->app->config->item('jwt_key');
        $this->tokenAlgorithm  = $this->app->config->item('jwt_algorithm');
    }

    /**
     * Generate Token
     * @param: user data
     */
    public function generateToken($data)
    {
        try {
            return JWT::encode($data, $this->tokenKey, $this->tokenAlgorithm);
        }
        catch(Exception $e) {
            return 'Message: ' .$e->getMessage();
        }
    }

    /**
     * Validate Token with Header
     * @return : user informations
     */
    public function validateToken()
    {
        /**
         * Request All Headers
         */
        $headers = $this->app->input->requestHeaders();

        
        /**
         * Authorization Header Exists
         */
        $tokenData = $this->tokenIsExist($headers);

        if($tokenData['status'] === true)
        {
            try
            {
                /**
                 * Token Decode
                 */
                try {
                    $token = explode(' ', $headers[$tokenData['key']]);
                    $headerToken = $headers[$tokenData['key']];
                    if($token[0] === 'Bearer'){
                        $headerToken = $token[1];
                    }
                    $tokenDecode = JWT::decode($headerToken, $this->tokenKey, array($this->tokenAlgorithm));

                    // $tokenDecode = JWT::decode($headers[$tokenData['key']], $this->tokenKey, array($this->tokenAlgorithm));
                }
                catch(Exception $e) {
                    return ['status' => false, 'message' => $e->getMessage()];
                }
                
                if(!empty($tokenDecode) AND is_object($tokenDecode))
                {
                    // Check User ID (exists and numeric)
                    // if(empty($tokenDecode->id) OR !is_numeric($tokenDecode->id)) 
                    // {
                    //     return ['status' => false, 'message' => 'User ID Not Define!'];

                    // // Check Token Time
                    // }else
                    if (empty($tokenDecode->time OR !is_numeric($tokenDecode->time))) {
                        
                        return ['status' => false, 'message' => 'Token Time Not Define!'];
                    } else {
                        /**
                         * Check Token Time Valid 
                         */
                        $timeDifference = strtotime('now') - $tokenDecode->time;
                        if( $timeDifference >= $this->tokenExpireTime )
                        {
                            return ['status' => false, 'message' => 'Token Time Expire.'];

                        } else {
                            /**
                             * All Validation false Return Data
                             */
                            return ['status' => true, 'data' => $tokenDecode];
                        }
                    }
                    
                } else {
                    return ['status' => false, 'message' => 'Forbidden'];
                }
            } catch(Exception $e) {
                return ['status' => false, 'message' => $e->getMessage()];
            }
        } else {
            // Authorization Header Not Found!
            return ['status' => false, 'message' => $tokenData['message'] ];
        }
    }

    /**
     * Validate Token with POST Request
     */
    public function validateTokenPost()
    {
        if (isset($_POST['token'])) {

            $token = $this->app->input->post('token', true);

            if (!empty($token) AND is_string($token) AND !is_array($token)) {
                
                try {
                    /**
                     * Token Decode
                     */
                    try {
                        $tokenDecode = JWT::decode($token, $this->tokenKey, array($this->tokenAlgorithm));
                    } catch(Exception $e) {
                        return ['status' => false, 'message' => $e->getMessage()];
                    }
    
                    if (!empty($tokenDecode) AND is_object($tokenDecode)) {
                        // Check User ID (exists and numeric)
                        if (empty($tokenDecode->id) OR !is_numeric($tokenDecode->id)) {
                            return ['status' => false, 'message' => 'User ID Not Define!'];
    
                        // Check Token Time
                        } else if (empty($tokenDecode->time OR !is_numeric($tokenDecode->time))) {
                            
                            return ['status' => false, 'message' => 'Token Time Not Define!'];
                        } else {
                            /**
                             * Check Token Time Valid 
                             */
                            $timeDifference = strtotime('now') - $tokenDecode->time;
                            
                            if ( $timeDifference >= $this->tokenExpireTime ) {
                                return ['status' => false, 'message' => 'Token Time Expire.'];
    
                            } else {
                                /**
                                 * All Validation false Return Data
                                 */
                                return ['status' => true, 'data' => $tokenDecode];
                            }
                        }
                        
                    } else{
                        return ['status' => false, 'message' => 'Forbidden'];
                    }
                } catch(Exception $e) {
                    return ['status' => false, 'message' => $e->getMessage()];
                }
            } else {
                return ['status' => false, 'message' => 'Token is not defined.' ];
            }
        } else {
            return ['status' => false, 'message' => 'Token is not defined.'];
        }
    }

    /**
     * Token Header Check
     * @param: request headers
     */
    public function tokenIsExist($headers)
    {
        if (!empty($headers) AND is_array($headers)) {

            foreach ($this->tokenHeader as $key) {
                if (array_key_exists($key, $headers) AND !empty($key)) {
                    return ['status' => true, 'key' => $key];
                }
            }

        }
        return ['status' => false, 'message' => 'Token is not defined.'];
    }

    /**
     * Fetch User Data
     * -----------------
     * @param: token
     * @return: user_data
     */
    public function userData()
    {
        /**
         * Request All Headers
         */
        $headers = $this->app->input->requestHeaders();

        /**
         * Authorization Header Exists
         */
        $tokenData = $this->tokenIsExist($headers);

        if($tokenData['status'] === true) {
            try {
                /**
                 * Token Decode
                 */
                try {
                    $tokenDecode = JWT::decode($headers[$tokenData['key']], $this->tokenKey, array($this->tokenAlgorithm));
                } catch(Exception $e) {
                    return ['status' => false, 'message' => $e->getMessage()];
                }

                if (!empty($tokenDecode) AND is_object($tokenDecode)) {
                    return $tokenDecode;
                } else {
                    return ['status' => false, 'message' => 'Forbidden'];
                }

            } catch(Exception $e) {
                return ['status' => false, 'message' => $e->getMessage()];
            }
        } else {
            // Authorization Header Not Found!
            return ['status' => false, 'message' => $tokenData['message'] ];
        }
    }
}
