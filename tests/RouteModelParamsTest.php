<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use Fyre\DB\Handlers\Mysql\MysqlConnection;
use Fyre\DB\TypeParser;
use Fyre\Entity\EntityLocator;
use Fyre\Error\Exceptions\NotFoundException;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\ORM\BehaviorRegistry;
use Fyre\ORM\ModelRegistry;
use Fyre\Router\Middleware\RouterMiddleware;
use Fyre\Router\Middleware\SubstituteBindingsMiddleware;
use Fyre\Router\Router;
use Fyre\Schema\SchemaRegistry;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use Fyre\Utility\Inflector;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Controller\ItemsController;
use Tests\Mock\Entity\Contain;
use Tests\Mock\Entity\Item;

final class RouteModelParamsTest extends TestCase
{
    protected Container $container;

    protected Connection $db;

    protected ModelRegistry $modelRegistry;

    protected Router $router;

    public function testProcessClosureRouteModelParams(): void
    {
        $ran = false;

        $function = function(Item $item) use (&$ran): string {
            $ran = true;

            $this->assertSame(
                'Test',
                $item->name
            );

            return '';
        };

        $this->router->connect('test/{item}', $function);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
            SubstituteBindingsMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/1',
                    ],
                ],
            ],
        ]);

        $response = $handler->handle($request);

        $this->assertTrue($ran);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );
    }

    public function testProcessClosureRouteModelParamsInvalid(): void
    {
        $this->expectException(NotFoundException::class);

        $function = function(Item $item): string {
            return '';
        };

        $this->router->connect('test/{item}', $function);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
            SubstituteBindingsMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/2',
                    ],
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

            $this->assertNull($item);

            return '';
        };

        $this->router->connect('test/{item}', $function);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
            SubstituteBindingsMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/2',
                    ],
                ],
            ],
        ]);

        $response = $handler->handle($request);

        $this->assertTrue($ran);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );
    }

    public function testProcessClosureRouteModelParamsParent(): void
    {
        $ran = false;

        $function = function(Item $item, Contain $contain) use (&$ran): string {
            $ran = true;

            $this->assertSame(
                'Test',
                $item->name
            );

            $this->assertSame(
                2,
                $contain->value
            );

            return '';
        };

        $this->router->connect('test/{item}/{contain}', $function);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
            SubstituteBindingsMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/1/1',
                    ],
                ],
            ],
        ]);

        $response = $handler->handle($request);

        $this->assertTrue($ran);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );
    }

    public function testProcessClosureRouteModelParamsParentInvalid(): void
    {
        $this->expectException(NotFoundException::class);

        $function = function(Item $item, Contain $contain) use (&$ran): string {
            return '';
        };

        $this->router->connect('test/{item}/{contain}', $function);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
            SubstituteBindingsMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/1/2',
                    ],
                ],
            ],
        ]);

        $handler->handle($request);
    }

    public function testProcessControllerRouteModelParams(): void
    {
        $this->router->connect('test/{item}', ItemsController::class);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
            SubstituteBindingsMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/1',
                    ],
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

        $this->router->connect('test/{item}', ItemsController::class);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
            SubstituteBindingsMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/2',
                    ],
                ],
            ],
        ]);

        $handler->handle($request);
    }

    public function testProcessControllerRouteModelParamsNullable(): void
    {
        $this->router->connect('test/{item}', [ItemsController::class, 'test']);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
            SubstituteBindingsMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/2',
                    ],
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
        $this->container = new Container();
        $this->container->singleton(TypeParser::class);
        $this->container->singleton(Config::class);
        $this->container->singleton(Inflector::class);
        $this->container->singleton(ConnectionManager::class);
        $this->container->singleton(SchemaRegistry::class);
        $this->container->singleton(ModelRegistry::class);
        $this->container->singleton(BehaviorRegistry::class);
        $this->container->singleton(EntityLocator::class);
        $this->container->singleton(Router::class);
        $this->container->use(Config::class)
            ->set('App.locale', 'en')
            ->set('Database', [
                'default' => [
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
                ],
            ]);

        $this->modelRegistry = $this->container->use(ModelRegistry::class);
        $this->modelRegistry->addNamespace('Tests\Mock\Model');

        $this->container->use(BehaviorRegistry::class)->addNamespace('Tests\Mock\Behaviors');
        $this->container->use(EntityLocator::class)->addNamespace('Tests\Mock\Entity');

        $this->db = $this->container->use(ConnectionManager::class)->use();

        $this->router = $this->container->use(Router::class);

        $this->db->query('DROP TABLE IF EXISTS contains');
        $this->db->query('DROP TABLE IF EXISTS items');

        $this->db->query(<<<'EOT'
            CREATE TABLE items (
                id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                PRIMARY KEY (id)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        EOT);

        $this->db->query(<<<'EOT'
            CREATE TABLE contains (
                id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                item_id INT(10) UNSIGNED NOT NULL,
                value INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (id)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        EOT);

        $Items = $this->modelRegistry->use('Items');
        $Contains = $this->modelRegistry->use('Contains');

        $item = $Items->newEntity([
            'name' => 'Test',
        ]);

        $Items->save($item);

        $contains = $Contains->newEntities([
            [
                'item_id' => 1,
                'value' => 2,
            ],
            [
                'item_id' => 2,
                'value' => 3,
            ],
        ]);

        $Contains->saveMany($contains);
    }

    protected function tearDown(): void
    {
        $this->db->query('DROP TABLE IF EXISTS contains');
        $this->db->query('DROP TABLE IF EXISTS items');
    }
}
