<?php
declare(strict_types=1);

namespace Fyre\Router\Middleware;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function is_string;

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

        $middleware = Router::getRoute()->getMiddleware();

        $processRoute = function(ServerRequest $request, RequestHandler $handler): ClientResponse {
            $response = $handler->handle($request);

            $result = Router::getRoute()->process($request, $response);

            if (is_string($result)) {
                return $response->setBody($result);
            }

            return $result;
        };

        if ($middleware === []) {
            return $processRoute($request, $handler);
        }

        $middleware[] = $processRoute;

        $response = $handler->handle($request);

        $queue = new MiddlewareQueue($middleware);
        $innerHandler = new RequestHandler($queue, $response);

        return $innerHandler->handle($request);
    }
}
