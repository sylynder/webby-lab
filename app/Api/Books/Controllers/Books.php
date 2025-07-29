<?php 

use Base\Controllers\ApiController;
use OpenApi\Attributes as OA;

class Books extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    #[OA\Get(path: '/api/data.json')]
    #[OA\Response(response: '200', description: 'The data')]
    public function getResource()
    {
        // ...
    }

    // #[OA\Put(path: '/add/users/')]
    #[OA\Get(path: '/add/users/', tags: ["Books"])]
    #[OA\Response(response: 200, description: 'Add users details')]
    #[OA\Response(response: 404, description: 'Error add user')]
    public function index_get()
    {
        // Sample Code Here ...
    }

    public function index_post()
    {
        // Sample Code Here ...
    }

    public function index_put($id)
    {
        $id = clean($id);

        // Sample Code Here ...
    }

    public function index_delete($id)
    {
        $id = clean($id);
        
        // Sample Code Here ...
    }
}
/* End of Books file */
