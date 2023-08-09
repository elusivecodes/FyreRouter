<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use Fyre\Router\Route;
use Fyre\Server\ClientResponse;
use Fyre\Server\RedirectResponse;
use Fyre\Server\ServerRequest;

use function preg_replace;

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
        return new RedirectResponse($this->destination);
    }

    /**
     * Set the route arguments from a path.
     * @param string $path The path.
     * @return Route A new Route.
     */
    public function setArgumentsFromPath(string $path): static
    {
        $temp = clone $this;

        $temp->destination = preg_replace($temp->getPathRegExp(), $temp->destination, $path);

        return $temp;
    }

}
