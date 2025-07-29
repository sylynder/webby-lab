<?php

use Base\Controllers\WebController;

class Sync extends WebController
{
    public function __construct()
    {
        parent::__construct();

        // $this->use->database(); // enable to use database

    }

    public function index()
    {
        // $promise = React\Async\async(function (): int {
        //     $browser = new \React\Http\Browser();
        //     $urls = [
        //         'https://developerkwame.com',
        //         'https://sylynder.com'
        //     ];

        //     $bytes = 0;
        //     foreach ($urls as $url) {
        //         $response = React\Async\await($browser->get($url));
        //         assert($response instanceof Psr\Http\Message\ResponseInterface);
        //         $bytes += $response->getBody()->getSize();
        //     }
        //     return $bytes;
        // })();

        // $promise->then(function (int $bytes) {
        //     echo 'Total size: ' . $bytes . PHP_EOL;
        // }, function (Exception $e) {
        //     echo 'Error: ' . $e->getMessage() . PHP_EOL;
        // });

        $client = new React\Http\Browser();

        $client->get('https://developerkwame.xyz/')->then(function (Psr\Http\Message\ResponseInterface $response) {
            echo "<pre>";
            var_export($response->getHeaders());

            $view = (string)$response->getBody();

            // dd($view);

            var_dump($view);
        }, function (Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
        });
    }

    public function reactphp()
    {

        $http = new React\Http\HttpServer(function (Psr\Http\Message\ServerRequestInterface $request) {
            return React\Http\Message\Response::plaintext(
                "Hello World!\n"
            );
        });

        $socket = new React\Socket\SocketServer('127.0.0.1:8007');
        $http->listen($socket);

        echo "Server running at http://127.0.0.1:8007" . PHP_EOL;
    }

    public function create()
    {
        // Sample Code Here ...
    }

    public function store()
    {
        // Sample Code Here ...
    }

    public function edit($id)
    {
        $id = clean($id);

        // Sample Code Here ...
    }

    public function update($id)
    {
        $id = clean($id);

        // Sample Code Here ...
    }

    public function delete($id)
    {
        $id = clean($id);

        // Sample Code Here ...
    }
}
/* End of Sync file */
