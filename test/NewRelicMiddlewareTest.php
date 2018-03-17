<?php

declare(strict_types=1);

namespace Cleeng\Test\NewRelicMiddleware;

use Cleeng\NewRelicMiddleware\NewRelicMiddleware;
use Intouch\Newrelic\Newrelic;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;

class NewRelicMiddlewareTest extends TestCase
{
    /** @var Newrelic */
    private $newrelic;

    /** @var NewRelicMiddleware */
    private $middleware;

    public function setUp()
    {
        $this->newrelic = $this->prophesize(Newrelic::class);
        $this->middleware = new NewRelicMiddleware($this->newrelic->reveal());
    }

    public function testByDefaultTransactionIsNamedFromPath()
    {
        $request = new ServerRequest([], [],'/api/ping');
        $delegate = function ($req, $res) { return new Response(); };
        $this->newrelic->nameTransaction('[GET] /api/ping')->shouldBeCalled();
        ($this->middleware)(
            $request,
            new Response(),
            $delegate
        );
    }

    public function testTransactionIsNamedFromRouteNameIfRouteResultIsAvailable()
    {
        $route = $this->prophesize(Route::class);
        $route->getPath()->willReturn('/api/user/:id');
        $routeResult = $this->prophesize(RouteResult::class);
        $routeResult->getMatchedRoute()->willReturn($route->reveal());
        $request = (new ServerRequest([], [],'/api/user/123123', 'GET'))
            ->withAttribute(RouteResult::class, $routeResult->reveal());
        $delegate = function ($req, $res) { return new Response(); };
        $this->newrelic->nameTransaction('[GET] /api/user/:id')->shouldBeCalled();
        ($this->middleware)(
            $request,
            new Response(),
            $delegate
        );
    }
}
