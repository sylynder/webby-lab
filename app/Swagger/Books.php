<?php

namespace App\Swagger;

use Base\Http\Restful;
use Base\Http\HttpStatus;
use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAA;



class Books extends Restful
{

    /**
     * @OA\PathItem(
     *     path="/api/users",
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     * @OA\Post(
     *     path="/v1/books/{bookid}",
     *     tags={"Books"},
     *   @OA\Parameter(name="bookid",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *    @OA\Response(response="200", description="Success"),
     *    @OA\Response(response="401", description="Unauthorized"),
     *    @OA\Response(response="404", description="Not Found"),
     *    @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(mediaType="application/json",
     *       @OA\Schema(
     *          @OA\Property(property="email",description="Login Email.",type="string"),
     *          @OA\Property(property="password",description="Login Password.",type="string"),
     *        ),
     *       ),
     *      ),
     *    security={{"api_key": {}}},
     * )
     * 
     * 
     */
    public function index()
    {

    }

    #[OAA\Get(path: '/books/update', tags: ["Books"])]
    #[OAA\Response(response: 200, description: 'AOK')]
    #[OAA\Response(response: 401, description: 'Not allowed')]
    public function update() { /* ... */ }

}