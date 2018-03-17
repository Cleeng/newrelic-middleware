<?php

declare(strict_types=1);

namespace Cleeng\NewRelicMiddleware;

use Intouch\Newrelic\Newrelic;
use Psr\Container\ContainerInterface;

class NewRelicMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): NewRelicMiddleware
    {
        return new NewRelicMiddleware($container->get(Newrelic::class));
    }
}
