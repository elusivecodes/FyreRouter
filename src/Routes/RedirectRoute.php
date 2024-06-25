<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use Fyre\Router\Route;
use Fyre\Server\ClientResponse;
use Fyre\Server\RedirectResponse;
use Fyre\Server\ServerRequest;

use function preg_replace_callback;

/**
 * RedirectRoute
 */
class RedirectRoute extends Route
{
    /**
     * New RedirectRoute constructor.
     *
     * @param string $destination The route destination.
     * @param string $path The route path.
     */
    public function __construct(string $destination, string $path = '')
    {
        parent::__construct($destination, $path);
    }

    /**
     * Process the route.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse The ClientResponse.
     */
    public function process(ServerRequest $request, ClientResponse $response): ClientResponse
    {
        $destination = preg_replace_callback(
            '/\\$(\d+)/',
            fn(array $match): string => $this->arguments[$match[1] - 1] ?? '',
            $this->destination
        );

        return new RedirectResponse($destination);
    }
}
