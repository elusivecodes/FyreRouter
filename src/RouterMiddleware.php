<?php
declare(strict_types=1);

namespace Fyre\Router;

use
    Fyre\Middleware\Middleware,
    Fyre\Middleware\RequestHandler,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest;

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
