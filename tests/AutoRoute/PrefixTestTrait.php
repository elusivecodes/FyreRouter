<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;

trait PrefixTestTrait
{

    public function testNamespacePrefix(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Mock\Controller', 'prefix');

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/deep/example/alt-method'
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
            '\Tests\Mock\Controller\Deep\ExampleController',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testNamespacePrefixLeadingSlash(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Mock\Controller', '/prefix');

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/deep/example/alt-method'
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
            '\Tests\Mock\Controller\Deep\ExampleController',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testNamespacePrefixTrailingSlash(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Mock\Controller', 'prefix/');

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/deep/example/alt-method'
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
            '\Tests\Mock\Controller\Deep\ExampleController',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testNamespacePrefixBuild(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Mock\Controller', 'prefix');

        $this->assertSame(
            '/prefix/deep/example/alt-method',
            Router::build([
                'controller' => '\Tests\Mock\Controller\Deep\Example',
                'action' => 'altMethod'
            ])
        );
    }

}
