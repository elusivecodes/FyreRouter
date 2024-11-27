<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use Fyre\Container\Container;
use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Route;
use Fyre\Server\ClientResponse;
use Fyre\Server\RedirectResponse;
use Fyre\Server\ServerRequest;

use function array_key_exists;
use function explode;
use function preg_replace_callback;
use function str_contains;

/**
 * RedirectRoute
 */
class RedirectRoute extends Route
{
    /**
     * New RedirectRoute constructor.
     *
     * @param Container $container The Container.
     * @param string $destination The destination.
     * @param string $path The path.
     * @param array $options The route options.
     */
    public function __construct(Container $container, string $destination, string $path = '', array $options = [])
    {
        parent::__construct($container, $destination, $path, $options);
    }

    /**
     * Process the route.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse The ClientResponse.
     */
    protected function process(ServerRequest $request, ClientResponse $response): ClientResponse
    {
        $destination = preg_replace_callback(
            '/\{([^\}]+)\}/',
            function(array $match): string {
                $name = $match[1];

                if (str_contains($name, ':')) {
                    [$name, $field] = explode(':', $name, 2);
                }

                if (!array_key_exists($name, $this->arguments)) {
                    throw RouterException::forMissingRouteParameter($name);
                }

                return $this->arguments[$name];
            },
            $this->destination
        );

        return new RedirectResponse($destination);
    }
}
