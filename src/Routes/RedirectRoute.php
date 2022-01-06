<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use
    Fyre\Router\Route,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest;

use function
    preg_replace;

/**
 * RedirectRoute
 */
class RedirectRoute extends Route
{

    /**
     * New RedirectRoute constructor.
     * @param string $destination The route destination.
     * @param string $path The route path.
     * @param array $methods The Route methods.
     */
    public function __construct(string $destination, string $path = '', array $methods = [])
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
        return $response->redirect($this->destination);
    }

    /**
     * Set the route arguments from a path.
     * @param string $path The path.
     * @return Route The Route.
     */
    public function setArgumentsFromPath(string $path): static
    {
        $this->destination = preg_replace($this->getPathRegExp(), $this->destination, $path);

        return $this;
    }

}
