<?php
namespace Mixten;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\ApplicationActionFixture;
use Tests\Fixtures\MiddlewareTest;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class MixtenTest extends TestCase
{
    public function testApplicationRouteUsingCallable()
    {
        $mixten = new Mixten();
        $mixten->buildContainer();
        $mixten->bootstrap();

        $mixten->get('/example', function() {
            $resp = new Response();
            $resp->getBody()->write('Hello');
            return $resp;
        });

        $request = ServerRequestFactory::fromGlobals([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/example',
        ]);

        $response = $mixten->run($request, true);
        $this->assertEquals('Hello', $response->getBody());
    }

    public function testApplicationRouteUsingAnActionFixture()
    {
        $mixten = new Mixten();
        $mixten->buildContainer();
        $mixten->bootstrap();

        $mixten->get('/example2', cc(ApplicationActionFixture::class, 'update'));

        $request = ServerRequestFactory::fromGlobals([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/example2',
        ]);

        $response = $mixten->run($request, true);
        $this->assertEquals('example', $response->getBody());
    }

    public function testApplicationMiddlewareWithNamedRoutes()
    {
        $mixten = new Mixten();
        $mixten->buildContainer();
        $mixten->bootstrap();

        $mixten->get('/example/{id}', cc(ApplicationActionFixture::class, 'update'))
            ->add(MiddlewareTest::class)
        ;

        $request = ServerRequestFactory::fromGlobals([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/example/2',
        ]);

        $response = $mixten->run($request, true);
        $this->assertEquals('Hello World', $response->getBody());
    }
}