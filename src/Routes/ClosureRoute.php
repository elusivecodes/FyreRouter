<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use Closure;
use Fyre\Router\Route;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function call_user_func;

/**
 * ClosureRoute
 */
class ClosureRoute extends Route
{

    /**
     * New ClosureRoute constructor.
     * @param Closure $destination The route destination.
     * @param string $path The route path.
     * @param array $methods The Route methods.
     */
    public function __construct(Closure $destination, string $path = '', array $methods = [])
    {
        parent::__construct($destination, $path, $methods);
    }

    /**
     * Process the route.
     * @param ServerRequest $request The ServerRequest.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse The ClientResponse.
     */
    public function process(ServerRequest $request, ClientResponse $response): ClientResponse
    {
        return call_user_func($this->destination, $request, $response, ...$this->arguments);
    }

}
