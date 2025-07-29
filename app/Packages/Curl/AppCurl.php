<?php

namespace App\Packages\Curl;

use Curl\Curl;
class AppCurl extends Curl
{
    public $curl;

    private $baseUrl;

    public function __construct($base_url = null, $options = [])
    {
        parent::__construct($base_url, $options);
    }

    public function baseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl . '/';
    }

    public function get($endpoint, $data = []) 
    {
        $this->get($this->baseUrl . $endpoint, $data);
        
        if ($this->curl->error) {
            echo 'Error: ' . $this->curl->errorMessage . "\n";
            $this->curl->diagnose();
        } else {
            // return is_array($curl->response) ? dd($curl->response) : dd([]);
            return $this->curl->response;
        }
    }

    public function post($endpoint, $data)
    {   

    }


}