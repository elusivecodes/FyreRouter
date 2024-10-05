<?php
declare(strict_types=1);

namespace Fyre\Router\Middleware;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function explode;
use function implode;
use function is_string;
use function preg_replace_callback;
use function str_contains;

/**
 * RouterMiddleware
 */
class RouterMiddleware extends Middleware
{
    /**
     * Process a ServerRequest.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @return ClientResponse The ClientResponse.
     */
    public function process(ServerRequest $request, RequestHandler $handler): ClientResponse
    {
        Router::loadRoute($request);

        $route = Router::getRoute();
        $routeMiddleware = $route->getMiddleware();

        $processRoute = function(ServerRequest $request, RequestHandler $handler): ClientResponse {
            $response = $handler->handle($request);

            $result = Router::getRoute()->process($request, $response);

            if (is_string($result)) {
                return $response->setBody($result);
            }

            return $result;
        };

        if ($routeMiddleware === []) {
            return $processRoute($request, $handler);
        }

        foreach ($routeMiddleware as $i => $middleware) {
            if (!is_string($middleware) || !str_contains($middleware, ':')) {
                continue;
            }

            $routeArgs ??= $route->getArguments();
            [$alias, $args] = explode(':', $middleware, 2);

            $args = preg_replace_callback(
                '/{(\d+)}/',
                fn(array $matches): string => $routeArgs[$matches[1] - 1] ?? '',
                $args
            );

            $routeMiddleware[$i] = implode(':', [$alias, $args]);
        }

        $routeMiddleware[] = $processRoute;

        $response = $handler->handle($request);

        $innerQueue = new MiddlewareQueue($routeMiddleware);
        $innerHandler = new RequestHandler($innerQueue, $response);

        return $innerHandler->handle($request);
    }
}
