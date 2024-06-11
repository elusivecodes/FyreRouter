<?php
declare(strict_types=1);

namespace Tests\Router;

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
    use ServerRequestTestTrait;
    use UrlTestTrait;

    protected function setUp(): void
    {
        Router::clear();
    }

}
