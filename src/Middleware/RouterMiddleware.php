<?php
declare(strict_types=1);

namespace Fyre\Router\Middleware;

use Closure;
use Fyre\Middleware\Middleware;
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
        $request = Router::loadRoute($request);

        $route = $request->getParam('route');
        $routeMiddleware = $route->getMiddleware();

        if ($routeMiddleware !== []) {
            Closure::bind(function() use ($route, $routeMiddleware): void {
                $this->queue = clone $this->queue;

                foreach ($routeMiddleware as $middleware) {
                    if (!is_string($middleware) || !str_contains($middleware, ':')) {
                        $this->queue->add($middleware);

                        continue;
                    }

                    $routeArgs ??= $route->getArguments();
                    [$alias, $args] = explode(':', $middleware, 2);

                    $args = preg_replace_callback(
                        '/{(\d+)}/',
                        fn(array $matches): string => $routeArgs[$matches[1] - 1] ?? '',
                        $args
                    );

                    $middleware = implode(':', [$alias, $args]);

                    $this->queue->add($middleware);
                }
            }, $handler, $handler)();
        }

        $response = $handler->handle($request);

        $result = $route->process($request, $response);

        if (is_string($result)) {
            return $response->setBody($result);
        }

        return $result;
    }
}
