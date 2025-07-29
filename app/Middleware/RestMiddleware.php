<?php

namespace App\Middleware;

use Base\Http\HttpStatus;
use Base\Controllers\RestController;

class RestMiddleware extends RestController
{
    public $validate;

    public function __construct()
    {
        parent::__construct();

        $this->use->library('form_validation');
        $this->validate = $this->form_validation;

        $this->output->setHeader('Access-Control-Allow-Origin: *');
    }

    protected function middleware() {}

    /**
     * Returns the request parameters 
     * given in the request body.
     *
     * @return array|string the request parameters 
     * given in the request body.
     */
    public function getContent($asArray = false)
    {
        $content = clean(input()->post());

        if (is_null($content) || empty($content)) {
            $content = json_decode(clean(input()->raw_input_stream), true);
        }

        if ($asArray) {
            return !is_null($content) ? $content : [];
        }

        return !is_null($content) ? json_encode($content) : [];
    }

    /**
     * Basically Allow CORS
     *
     * @return void
     */
    protected function allowCors()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: *');
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method == "OPTIONS") {
            die();
        }
    }

    protected function successResponse($data, $statusCode = HttpStatus::OK)
    {
        $status = ['status' => true];
        $data = array_merge($status, $data);
        return $this->response->json($data, $statusCode);
    }

    protected function errorResponse($data, $statusCode = HttpStatus::NOT_FOUND)
    {
        $status = ['status' => false];
        $data = array_merge($status, $data);
        return $this->response->json($data, $statusCode);
    }

}
