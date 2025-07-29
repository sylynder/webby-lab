<?php

namespace App\Swagger;

use Base\Http\Restful;
use Base\Http\HttpStatus;
use OpenApi\Annotations as OA;

class Auth extends Restful
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @OA\PathItem(
     *     path="/api/users",
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     * @OA\Post(
     *     path="/v1/Auth/{username}",
     *     tags={"Auth"},
     *   @OA\Parameter(name="username",
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
        * @OA\Info(
        *     description="This is a Sample Swagger For Webby.
        *  [irc.freenode.net, #swagger](https://wbbby.sylynder.com).",
        *     version="1.0.0",
        *     title="Swagger For Webby",
        *     termsOfService="https://webby.sylynder.com/docs",
        *     @OA\Contact(
        *         email="developerkwame@gmail.com"
        *     ),
        *     @OA\License(
        *         name="Apache 2.0",
        *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
        *     )
        * )
     * 
     */
    public function index_v1_post($username = "Mai")
    {
        response()->json([
            'status' => true,
            'data' => $username,
            'get' => post(),
            'message' => 'index_v1_get found'
        ], HttpStatus::OK);
    }
}
