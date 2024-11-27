<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use Fyre\Container\Container;
use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Route;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use ReflectionClass;

use function array_shift;
use function class_exists;
use function method_exists;

/**
 * ControllerRoute
 */
class ControllerRoute extends Route
{
    protected string $action;

    protected string $controller;

    /**
     * New ControllerRoute constructor.
     *
     * @param Container $container The Container.
     * @param array|string $destination The destination.
     * @param string $path The path.
     * @param array $options The route options.
     */
    public function __construct(Container $container, array|string $destination, string $path = '', array $options = [])
    {
        parent::__construct($container, $destination, $path, $options);

        $destination = (array) $this->destination;

        $this->controller = array_shift($destination);
        $this->action = array_shift($destination) ?? 'index';
    }

    /**
     * Get the route controller action.
     *
     * @return string The route controller action.
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get the route controller class name.
     *
     * @return string The route controller class name.
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * Get the reflection parameters.
     *
     * @return array The reflection parameters.
     */
    public function getParameters(): array
    {
        if (!class_exists($this->controller) || !method_exists($this->controller, $this->action)) {
            return [];
        }

        return (new ReflectionClass($this->controller))
            ->getMethod($this->action)
            ->getParameters();
    }

    /**
     * Process the route.
     *
     * @param ServerRequest $request The ServerRequest.
     * @return ClientResponse|string The ClientResponse or string response.
     *
     * @throws RouterException if the controller class or method is not valid.
     */
    protected function process(ServerRequest $request, ClientResponse $response): ClientResponse|string
    {
        if (!class_exists($this->controller)) {
            throw RouterException::forInvalidController($this->controller);
        }

        if (!method_exists($this->controller, $this->action)) {
            throw RouterException::forInvalidMethod($this->controller, $this->action);
        }

        $controller = $this->container->build($this->controller, ['request' => $request, 'response' => $response]);

        return $this->container->call([$controller, $this->action], $this->arguments);
    }
}
