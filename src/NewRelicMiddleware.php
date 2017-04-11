<?php

namespace Cleeng\NewRelicMiddleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Intouch\Newrelic\Newrelic;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

class NewRelicMiddleware implements MiddlewareInterface
{
    /** @var Newrelic */
    private $newrelic;

    public function __construct(Newrelic $newrelic)
    {
        $this->newrelic = $newrelic;
    }

    private function detectTransactionName(ServerRequestInterface $request): string
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        if (!$routeResult) {
            return $request->getUri()->getPath();
        }
        /** @var RouteResult $routeResult */
        if (!$routeResult->getMatchedRoute()) {
            return $request->getUri()->getPath();
        }

        return $routeResult->getMatchedRoute()->getPath();
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $this->newrelic->nameTransaction($this->detectTransactionName($request));

        return $delegate->process($request);
    }
}
