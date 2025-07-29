<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class Users
{
    public function __invoke(ServerRequestInterface $request)
    {
        return Response::plaintext(
            "Hello " . $request->getAttribute('name') . "!\n"
        );
    }
}
