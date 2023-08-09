<?php
declare(strict_types=1);

namespace Fyre\Router\Middleware;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\RequestHandler;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

/**
 * RouterMiddleware
 */
class RouterMiddleware extends Middleware
{

    /**
     * Process a ServerRequest.
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @return ClientResponse The ClientResponse.
     */
    public function process(ServerRequest $request, RequestHandler $handler): ClientResponse
    {
        Router::loadRoute($request);

        $response = $handler->handle($request);

        return Router::getRoute()->process($request, $response);
    }

}
