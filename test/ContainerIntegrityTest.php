<?php

declare(strict_types=1);

namespace Cleeng\Test\NewRelicMiddleware;

use Blast\TestUtils\ServiceIntegrityTestTrait;
use Cleeng\NewRelicMiddleware\ConfigProvider;
use PHPUnit\Framework\TestCase;

class ContainerIntegrityTest extends TestCase
{
    use ServiceIntegrityTestTrait;

    private static function getConfig()
    {
        return (new ConfigProvider())();
    }

    private static function getServiceManagerConfigKey()
    {
        return 'dependencies';
    }
}
