<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\Middleware\MiddlewareRegistry;
use Fyre\Router\Router;
use Fyre\Router\Routes\ClosureRoute;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Router\Routes\RedirectRoute;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;

use function class_uses;

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

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(Router::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(ClosureRoute::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(ControllerRoute::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(RedirectRoute::class)
        );
    }

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->singleton(Config::class);
        $this->container->singleton(Router::class);
        $this->container->singleton(MiddlewareRegistry::class);
    }
}
