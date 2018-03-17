<?php

declare(strict_types=1);

namespace Cleeng\NewRelicMiddleware;

use Intouch\Newrelic\Newrelic;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

class NewRelicMiddleware
{
    /** @var Newrelic */
    private $newrelic;

    public function __construct(Newrelic $newrelic = null)
    {
        if (null === $newrelic) {
            $newrelic = new Newrelic();
        }
        $this->newrelic = $newrelic;
    }

    private function detectTransactionName(ServerRequestInterface $request): string
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        if (!$routeResult) {
            return $request->getUri()->getPath() ? : '';
        }
        /** @var RouteResult $routeResult */
        if (!$routeResult->getMatchedRoute()) {
            return $request->getUri()->getPath() ? : '';
        }

        return $routeResult->getMatchedRoute()->getPath();
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $this->newrelic->nameTransaction(
            '[' . $request->getMethod() . '] ' . $this->detectTransactionName($request)
        );

        return $next($request, $response);
    }
}
