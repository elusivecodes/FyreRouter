<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;

final class AutoRouteTest extends TestCase
{

    use BuildFromPathTestTrait;
    use BuildTestTrait;
    use FindRouteTestTrait;
    use PrefixTestTrait;

    public function testDelimiterFindRoute(): void
    {
        Router::setDelimiter('_');

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home/alt_method'
                ]
            ]
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Mock\Controller\HomeController',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testDelimiterBuild(): void
    {
        Router::setDelimiter('_');

        $this->assertSame(
            '/home/alt_method',
            Router::build([
                'controller' => 'Home',
                'action' => 'altMethod'
            ])
        );
    }

    protected function setUp(): void
    {
        Router::clear();
        Router::setAutoRoute(true);
        Router::setDelimiter('-');
        Router::addNamespace('Tests\Mock\Controller');
    }

}
