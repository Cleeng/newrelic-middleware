<?php

namespace Cleeng\Test\NewRelicMiddleware;

use Cleeng\NewRelicMiddleware\NewRelicMiddleware;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Intouch\Newrelic\Newrelic;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;
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
        $delegate = $this->prophesize(DelegateInterface::class);
        $delegate->process($request)->willReturn(new Response());
        $this->newrelic->nameTransaction('[GET] /api/ping')->shouldBeCalled();
        $this->middleware->process(
            $request,
            $delegate->reveal()
        );
    }

    public function testTransactionIsNamedFromRouteNameIfRouteResultIsAvailable()
    {
        $routeResult = $this->prophesize(RouteResult::class);
        $routeResult->getMatchedRouteName()->willReturn('api.create-user');
        $request = (new ServerRequest([], [],'/api/create-user', 'POST'))
            ->withAttribute(RouteResult::class, $routeResult->reveal());
        $delegate = $this->prophesize(DelegateInterface::class);
        $delegate->process($request)->willReturn(new Response());
        $this->newrelic->nameTransaction('[POST] api.create-user')->shouldBeCalled();
        $this->middleware->process(
            $request,
            $delegate->reveal()
        );
    }
}
