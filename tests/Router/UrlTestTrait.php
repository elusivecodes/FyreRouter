<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Config\Config;
use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Router;
use Tests\Mock\Controller\HomeController;

trait UrlTestTrait
{
    public function testUrl(): void
    {
        $router = $this->container->use(Router::class);

        $router->connect('home', HomeController::class, ['as' => 'home']);

        $this->assertSame(
            '/home',
            $router->url('home')
        );
    }

    public function testUrlArguments(): void
    {
        $router = $this->container->use(Router::class);

        $router->connect('home/alternate/{a}/{b}/{c}', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        $this->assertSame(
            '/home/alternate/test/a/2',
            $router->url('alternate', [
                'a' => 'test',
                'b' => 'a',
                'c' => 2,
            ])
        );
    }

    public function testUrlFragment(): void
    {
        $router = $this->container->use(Router::class);

        $router->connect('home/alternate/{a}', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        $this->assertSame(
            '/home/alternate/1#test',
            $router->url('alternate', [
                'a' => 1,
                '#' => 'test',
            ])
        );
    }

    public function testUrlFull(): void
    {
        $this->container->use(Config::class)->set('App.baseUri', 'https://test.com/');

        $router = $this->container->use(Router::class);

        $router = $this->container->build(Router::class);

        $router->connect('home/alternate/{a}', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        $this->assertSame(
            'https://test.com/home/alternate/1#test',
            $router->url('alternate', [
                'a' => 1,
                '#' => 'test',
            ], ['fullBase' => true])
        );
    }

    public function testUrlGroupAlias(): void
    {
        $router = $this->container->use(Router::class);

        $router->group(['as' => 'home.'], function(Router $router): void {
            $router->connect('alternate', [HomeController::class, 'altMethod'], ['as' => 'alt']);
        });

        $this->assertSame(
            '/alternate',
            $router->url('home.alt')
        );
    }

    public function testUrlGroupAliasDeep(): void
    {
        $router = $this->container->use(Router::class);

        $router->group(['as' => 'home.'], function(Router $router): void {
            $router->group(['as' => 'deep.'], function(Router $router): void {
                $router->connect('alternate', [HomeController::class, 'altMethod'], ['as' => 'alt']);
            });
        });

        $this->assertSame(
            '/alternate',
            $router->url('home.deep.alt')
        );
    }

    public function testUrlInvalid(): void
    {
        $this->expectException(RouterException::class);

        $router = $this->container->use(Router::class);

        $router->url('alternate');
    }

    public function testUrlInvalidArgument(): void
    {
        $this->expectException(RouterException::class);

        $router = $this->container->use(Router::class);

        $router->connect('home/alternate/{a}', [HomeController::class, 'altMethod'], [
            'as' => 'alternate',
            'placeholders' => [
                'a' => '\d+',
            ],
        ]);

        $router->url('alternate', [
            'a' => 'test',
        ]);
    }

    public function testUrlMissingArgument(): void
    {
        $this->expectException(RouterException::class);

        $router = $this->container->use(Router::class);

        $router->connect('home/alternate/{a}/{b}/{c}', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        $router->url('alternate', [
            'a' => 'test',
            'b' => 'a',
        ]);
    }

    public function testUrlQuery(): void
    {
        $router = $this->container->use(Router::class);

        $router->connect('home/alternate/{a}', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        $this->assertSame(
            '/home/alternate/1?test=2',
            $router->url('alternate', [
                'a' => 1,
                '?' => ['test' => 2],
            ])
        );
    }
}
