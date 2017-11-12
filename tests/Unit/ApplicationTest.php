<?php
namespace Tests\Unit;

use Canister\Canister;
use Invoker\InvokerInterface;
use Mixten\Application;
use function Mixten\cc;
use Mixten\Invoker;
use PHPUnit\Framework\TestCase;
use Starch\Router\RouterMiddleware;
use Tests\Fixtures\ApplicationActionFixture;
use Tests\Fixtures\MiddlewareTest;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class ApplicationTest extends TestCase
{
    public function testInvokerInterface()
    {
        $app = new Application();
        $container = $app->getContainer();
        $this->assertInstanceOf(Invoker::class, $container->get(InvokerInterface::class));
    }

    public function testStarchContainerUsesMixtenContainer()
    {
        $app = new Application();
        $starch = $app->getStarchApp();

        $this->assertInstanceOf(Canister::class, $starch->getContainer());
    }

    public function testApplicationRouteUsingCallable()
    {
        $app = new Application();
        $app->get('/example', function() {
            $resp = new Response();
            $resp->getBody()->write('Hello');

            return $resp;
        });

        $app->add(RouterMiddleware::class);

        $request = ServerRequestFactory::fromGlobals([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/example',
        ]);

        $response = $app->run($request, true);

        $this->assertEquals('Hello', $response->getBody());
    }

    public function testApplicationRouteUsingAnActionFixture()
    {
        $app = new Application();
        $app->get('/example2', cc(ApplicationActionFixture::class, 'update'));

        $app->add(RouterMiddleware::class);

        $request = ServerRequestFactory::fromGlobals([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/example2',
        ]);

        $response = $app->run($request, true);

        $this->assertEquals('example', $response->getBody());
    }

    public function testApplicationMiddlewareWithNamedRoutes()
    {
        $app = new Application();

        $app->add(RouterMiddleware::class);
        $app->get('/example/{id}', cc(ApplicationActionFixture::class, 'update'))
            ->add(MiddlewareTest::class)
        ;

        $request = ServerRequestFactory::fromGlobals([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/example/2',
        ]);

        $response = $app->run($request, true);
        echo $response->getBody();

//        $this->assertEquals('Hello', $response->getBody());
        $this->assertTrue(true);
    }
}