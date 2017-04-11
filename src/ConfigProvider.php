<?php

namespace Cleeng\NewRelicMiddleware;

use Intouch\Newrelic\Newrelic;
use Zend\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'factories' => [
                    Newrelic::class => InvokableFactory::class,
                    NewRelicMiddleware::class => NewRelicMiddlewareFactory::class,
                ],
            ],
        ];
    }
}
