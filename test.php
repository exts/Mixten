<?php

use Mixten\Mixten;
use Starch\App;
use Starch\Router\RouterMiddleware;
use Tests\Fixtures\MiddlewareTest;
use Zend\Diactoros\Request;
use Zend\Diactoros\ServerRequestFactory;

require 'vendor/autoload.php';

//$stackphp = new App();
//$stackphp->get('/test/{id}', function() {
//    return 'testing';
//});
//
//$stackphp->add(MiddlewareTest::class, '/test/{id}');
//$stackphp->add(RouterMiddleware::class);
//
//$server = ServerRequestFactory::fromGlobals([
//    'REQUEST_METHOD' => 'GET',
//    'REQUEST_URI' => '/test/2',
//]);
//$response = $stackphp->process($server);
//
//echo $response->getBody();

$mixten = new Mixten();
$mixten->bootstrap();
$mixten->get('/test/{id}', function() {
    return 'testing';
})->add(MiddlewareTest::class);

$server = ServerRequestFactory::fromGlobals([
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/test/2',
]);

$response = $mixten->run($server, true);

echo $response->getBody();

exit("\n\n");