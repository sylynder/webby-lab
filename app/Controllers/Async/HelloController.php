<?php

namespace App\Controllers\Async;

use React\Http\Message\Response;

class HelloController
{
    public function __invoke()
    {
        return Response::plaintext(
            "Hello wörld!\n"
        );
    }
}
