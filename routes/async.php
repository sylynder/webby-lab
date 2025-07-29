<?php



$app = new FrameworkX\App();

// $app->get('/', App\Controllers\HelloController::class);
$app->get('/users/{name}', App\Controllers\Users::class);

$app->get('/user/{id}', function (Psr\Http\Message\ServerRequestInterface $request) {
    $id = $request->getAttribute('id');
    if ($id === 'admin') {
        return \React\Http\Message\Response::html(
            "Forbidden\n"
        )->withStatus(\React\Http\Message\Response::STATUS_FORBIDDEN);
    }

    return React\Http\Message\Response::html(
        "Hello $id\n"
    );
});

$app->get('/see', function () {
    return React\Http\Message\Response::plaintext(
        "Hello wörld!\n"
    );
});

$app->get('/books/{name}', function (Psr\Http\Message\ServerRequestInterface $request) {
    return React\Http\Message\Response::plaintext(
        "Hello " . $request->getAttribute('name') . "!\n"
    );
});

$app->run();
