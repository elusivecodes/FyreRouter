<?php
declare(strict_types=1);

namespace Fyre\Router\Middleware;

use Closure;
use Fyre\Container\Container;
use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\MiddlewareRegistry;
use Fyre\Middleware\RequestHandler;
use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function array_key_exists;
use function array_map;
use function is_string;
use function str_contains;
use function str_ends_with;
use function str_starts_with;
use function substr;

/**
 * RouterMiddleware
 */
class RouterMiddleware extends Middleware
{
    protected Container $container;

    protected MiddlewareRegistry $middlewareRegistry;

    protected Router $router;

    /**
     * New RouterMiddleware constructor.
     *
     * @param Container $container The Container.
     * @param MiddlewareRegistry $middlewareRegistry The MiddlewareRegistry.
     * @param Router $router The Router.
     */
    public function __construct(Container $container, MiddlewareRegistry $middlewareRegistry, Router $router)
    {
        $this->container = $container;
        $this->middlewareRegistry = $middlewareRegistry;
        $this->router = $router;
    }

    /**
     * Handle a ServerRequest.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param Closure $next The next handler.
     * @return ClientResponse The ClientResponse.
     */
    public function handle(ServerRequest $request, Closure $next): ClientResponse
    {
        $request = $this->router->loadRoute($request);

        $response = $next($request);

        $route = $request->getParam('route');
        $routeMiddleware = $route->getMiddleware();

        if ($routeMiddleware === []) {
            return $route->handle($request, $response);
        }

        foreach ($routeMiddleware as $i => $middleware) {
            if (!is_string($middleware) || !str_contains($middleware, ':')) {
                continue;
            }

            [$alias, $args] = explode(':', $middleware, 2);
            $args = explode(',', $args);

            $routeMiddleware[$i] = function(ServerRequest $request, Closure $next) use ($alias, $args): ClientResponse {
                $route = $request->getParam('route');
                $routeArgs = $route->getArguments();

                $args = array_map(
                    function(string $arg) use ($routeArgs): mixed {
                        if (!str_starts_with($arg, '{') || !str_ends_with($arg, '}')) {
                            return $arg;
                        }

                        $arg = substr($arg, 1, -1);

                        if (!array_key_exists($arg, $routeArgs)) {
                            throw RouterException::forMissingRouteParameter($arg);
                        }

                        return $routeArgs[$arg] ?? null;
                    },
                    $args
                );

                $middleware = $this->middlewareRegistry->use($alias);

                if ($middleware instanceof Middleware) {
                    $middleware = $middleware->handle(...);
                }

                return $middleware($request, $next, ...$args);
            };
        }

        $routeMiddleware[] = fn(ServerRequest $request, Closure $next): ClientResponse => $request->getParam('route')->handle($request, $next($request));

        $routeQueue = new MiddlewareQueue($routeMiddleware);

        $routeHandler = $this->container->build(RequestHandler::class, [
            'queue' => $routeQueue,
            'initialResponse' => $response,
        ]);

        return $routeHandler->handle($request);
    }
}
