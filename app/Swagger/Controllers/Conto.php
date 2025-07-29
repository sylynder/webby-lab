<?php

namespace App\Swagger\Controllers;

use OpenApi\Attributes as OA;

// #[OA\Info(title: "My First API", version: "0.1")]
class Conto
{

    #[OA\Get(path: '/api/data.json', tags: ["Books"])]
    #[OA\Response(response: '200', description: 'The data')]
    public function getResource()
    {
        // ...
    }

    #[OA\Put(path: '/add/users/', tags: ["Books"])]
    #[OA\Response(response: 200, description: 'Add users details')]
    #[OA\Response(response: 404, description: 'Error add user')]
    public function index_get()
    {
        // Sample Code Here ...
    }
}
