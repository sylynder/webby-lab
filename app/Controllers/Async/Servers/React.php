<?php 

use React\Http\HttpServer;
use React\Socket\SocketServer;
use React\Http\Message\Response;
use Base\Controllers\ConsoleController;
use Psr\Http\Message\ServerRequestInterface;

class React extends ConsoleController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $http = new HttpServer(function (ServerRequestInterface $request) {
            return Response::plaintext(
                "Hello World!\n"
            );
        });
        
        $socket = new SocketServer('127.0.0.1:8005');
        $http->listen($socket);
        
        echo "Server running at http://127.0.0.1:8005" . PHP_EOL;
    }

}
/* End of React file */
