<?php
declare(strict_types=1);

namespace Tests;

use Fyre\DB\ConnectionManager;
use Fyre\DB\Handlers\Mysql\MysqlConnection;
use Fyre\Entity\EntityLocator;
use Fyre\Error\Exceptions\NotFoundException;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\ORM\ModelRegistry;
use Fyre\Router\Middleware\RouterMiddleware;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Controller\ItemsController;
use Tests\Mock\Entity\Item;

final class RouteModelParamsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ConnectionManager::clear();
        ConnectionManager::setConfig('default', [
            'className' => MysqlConnection::class,
            'host' => getenv('MYSQL_HOST'),
            'username' => getenv('MYSQL_USERNAME'),
            'password' => getenv('MYSQL_PASSWORD'),
            'database' => getenv('MYSQL_DATABASE'),
            'port' => getenv('MYSQL_PORT'),
            'collation' => 'utf8mb4_unicode_ci',
            'charset' => 'utf8mb4',
            'compress' => true,
            'persist' => false,
        ]);

        $connection = ConnectionManager::use();

        $connection->query('DROP TABLE IF EXISTS items');

        $connection->query(<<<'EOT'
            CREATE TABLE items (
                id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                PRIMARY KEY (id)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        EOT);
    }

    public static function tearDownAfterClass(): void
    {
        $connection = ConnectionManager::use();
        $connection->query('DROP TABLE IF EXISTS contains');
    }

    public function testProcessClosureRouteModelParams(): void
    {
        $ran = false;

        $function = function(Item $item) use (&$ran): string {
            $ran = true;

            return $item->name;
        };

        Router::connect('test/(\d+)', $function);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/1',
                ],
            ],
        ]);

        $response = $handler->handle($request);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );

        $this->assertSame(
            'Test',
            $response->getBody()
        );
    }

    public function testProcessClosureRouteModelParamsInvalid(): void
    {
        $this->expectException(NotFoundException::class);

        $function = function(Item $item): string {
            return $item->name;
        };

        Router::connect('test/(\d+)', $function);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/2',
                ],
            ],
        ]);

        $handler->handle($request);
    }

    public function testProcessClosureRouteModelParamsNullable(): void
    {
        $ran = false;

        $function = function(Item|null $item = null) use (&$ran): string {
            $ran = true;

            return '';
        };

        Router::connect('test/(\d+)', $function);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/2',
                ],
            ],
        ]);

        $response = $handler->handle($request);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );
    }

    public function testProcessControllerRouteModelParams(): void
    {
        Router::connect('test/(\d+)', ItemsController::class);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/1',
                ],
            ],
        ]);

        $response = $handler->handle($request);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );

        $this->assertSame(
            'Test',
            $response->getBody()
        );
    }

    public function testProcessControllerRouteModelParamsInvalid(): void
    {
        $this->expectException(NotFoundException::class);

        Router::connect('test/(\d+)', ItemsController::class);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/2',
                ],
            ],
        ]);

        $handler->handle($request);
    }

    public function testProcessControllerRouteModelParamsNullable(): void
    {
        Router::connect('test/(\d+)', [ItemsController::class, 'test']);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/2',
                ],
            ],
        ]);

        $response = $handler->handle($request);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );
    }

    protected function setUp(): void
    {
        Router::clear();

        EntityLocator::clear();
        EntityLocator::addNamespace('Tests\Mock\Entity');

        ModelRegistry::clear();
        ModelRegistry::addNamespace('Tests\Mock\Model');

        $model = ModelRegistry::use('Items');

        $item = $model->newEntity([
            'name' => 'Test'
        ]);

        $model->save($item);
    }

    protected function tearDown(): void
    {
        $connection = ConnectionManager::use();
        $connection->query('TRUNCATE items');
    }
}
