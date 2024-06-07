<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Controller\ErrorController;

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
    use ServerRequestTestTrait;
    use UrlTestTrait;

    public function testErrorRoute(): void
    {
        Router::setErrorRoute(ErrorController::class);

        $errorRoute = Router::getErrorRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $errorRoute
        );

        $this->assertSame(
            ErrorController::class,
            $errorRoute->getController()
        );
    }

    protected function setUp(): void
    {
        Router::clear();
    }

}
