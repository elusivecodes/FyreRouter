<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\Middleware\MiddlewareRegistry;
use Fyre\Router\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    use BaseUriTestTrait;
    use ConnectTestTrait;
    use DeleteTestTrait;
    use FindRouteTestTrait;
    use GetTestTrait;
    use MiddlewareTestTrait;
    use PatchTestTrait;
    use PlaceholderTestTrait;
    use PostTestTrait;
    use PrefixTestTrait;
    use PutTestTrait;
    use RedirectTestTrait;
    use UrlTestTrait;

    protected Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->singleton(Config::class);
        $this->container->singleton(Router::class);
        $this->container->singleton(MiddlewareRegistry::class);
    }
}
