<?php 

namespace App\Controllers\Swagger;

use Base\Controllers\WebController;
use OpenApi\Generator;

class GenDocs extends WebController
{
    public function __construct()
    {
        parent::__construct();
        
        // $this->use->database(); // enable to use database
    }

    public function index()
    {
        response()->json([
            'success' => true,
            'data' => [
                'message' => 'We are here'
            ]
        ]);
    }

    /**
     * Generate OpenAPI documentation for the API ...
     * @return string
     */
    public function generate(): string
    {
        // Specify the path where your API controllers are located
        $openapi = Generator::scan([APPROOT . 'Controllers']);

        $swaggerContent = $openapi->toJson();

        // Save the generated OpenAPI content to a file
        $filePath = FCPATH . 'swagger_ui/swagger.json';
        file_put_contents($filePath, $swaggerContent);

        return $swaggerContent;
    }

    /**
     * Render the SwaggerUI ...
     * @return string
     */
    public function swagger()
    {
        return view('swagger_docs/index');
    }
}