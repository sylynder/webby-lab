<?php

use Base\Controllers\WebController;

class Swagger extends WebController {

    public function __construct()
    {
        $this->load->library('session');
        header("Access-Control-Allow-Origin: *");
        parent::__construct();
    }

	public function index(){
		return view('swagger-ui/docs');
	}

    public function docs()
    {
        header('Content-type: application/json');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        // $openapi = \OpenApi\Generator::scan(['/application/controllers/']);
        
        // $openapi = \OpenApi\Generator::scan($_SERVER['DOCUMENT_ROOT'].'/application/controllers/');
        // $openapi = \OpenApi\Generator::scan([ROOTPATH . 'app/Swagger/', ROOTPATH . 'app/DataModels/']);
        $openapi = \OpenApi\Generator::scan([ROOTPATH . 'app/Swagger/']);
        
        header('Content-Type: application/json');
        echo $openapi->toJSON();

    }

}
