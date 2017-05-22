<?php
namespace Tests\Fixtures;

use Zend\Diactoros\Response;

class ActionFixtureTest
{
    public function example()
    {
        return 'example';
    }
}

class ApplicationActionFixture
{
    private $action_fixture_test;

    public function __construct(ActionFixtureTest $action_fixture_test)
    {
        $this->action_fixture_test = $action_fixture_test;
    }

    public function update()
    {
        $response = new Response();
        $response->getBody()->write($this->action_fixture_test->example());

        return $response;
    }
}