<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Router;
use Tests\Mock\Controller\HomeController;

trait UrlTestTrait
{
    public function testUrl(): void
    {
        Router::connect('home', HomeController::class, ['as' => 'home']);

        $this->assertSame(
            '/home',
            Router::url('home')
        );
    }

    public function testUrlArguments(): void
    {
        Router::connect('home/alternate/(.*)/(.*)/(.*)', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        $this->assertSame(
            '/home/alternate/test/a/2',
            Router::url('alternate', ['test', 'a', 2])
        );
    }

    public function testUrlFragment(): void
    {
        Router::connect('home/alternate/(.*)', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        $this->assertSame(
            '/home/alternate/1#test',
            Router::url('alternate', [1, '#' => 'test'])
        );
    }

    public function testUrlFull(): void
    {
        Router::setBaseUri('https://test.com/');

        Router::connect('home/alternate/(.*)', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        $this->assertSame(
            'https://test.com/home/alternate/1#test',
            Router::url('alternate', [1, '#' => 'test'], ['fullBase' => true])
        );
    }

    public function testUrlGroupAlias(): void
    {
        Router::group(['as' => 'home.'], function() {
            Router::get('alternate', [HomeController::class, 'altMethod'], ['as' => 'alt']);
        });

        $this->assertSame(
            '/alternate',
            Router::url('home.alt')
        );
    }

    public function testUrlGroupAliasDeep(): void
    {
        Router::group(['as' => 'home.'], function() {
            Router::group(['as' => 'deep.'], function() {
                Router::get('alternate', [HomeController::class, 'altMethod'], ['as' => 'alt']);
            });
        });

        $this->assertSame(
            '/alternate',
            Router::url('home.deep.alt')
        );
    }

    public function testUrlInvalid(): void
    {
        $this->expectException(RouterException::class);

        Router::url('alternate');
    }

    public function testUrlInvalidArgument(): void
    {
        $this->expectException(RouterException::class);

        Router::connect('home/alternate/(\d+)', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        Router::url('alternate', ['test']);
    }

    public function testUrlMissingArgument(): void
    {
        $this->expectException(RouterException::class);

        Router::connect('home/alternate/(.*)/(.*)/(.*)', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        Router::url('alternate', ['test', 'a']);
    }

    public function testUrlQuery(): void
    {
        Router::connect('home/alternate/(.*)', [HomeController::class, 'altMethod'], ['as' => 'alternate']);

        $this->assertSame(
            '/home/alternate/1?test=2',
            Router::url('alternate', [1, '?' => ['test' => 2]])
        );
    }
}
