<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Route;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function array_map;
use function array_shift;
use function class_exists;
use function explode;
use function method_exists;
use function preg_replace;

/**
 * ControllerRoute
 */
class ControllerRoute extends Route
{

    protected string $controller;

    protected string $action;

    /**
     * New ControllerRoute constructor.
     * @param string $destination The route destination.
     * @param string $path The route path.
     * @param array $methods The Route methods.
     */
    public function __construct(string $destination, string $path = '', array $methods = [])
    {
        if ($destination && $destination[0] !== '\\') {
            $destination = Router::getDefaultNamespace().$destination;
        }

        parent::__construct($destination, $path, $methods);

        $arguments = explode('/', $destination);
        $destination = array_shift($arguments);
        $destination = explode('::', $destination, 2);

        $this->controller = array_shift($destination).'Controller';
        $this->action = array_shift($destination) ?? 'index';
        $this->arguments = $arguments;
    }

    /**
     * Get the route controller action.
     * @return string The route controller action.
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get the route controller class name.
     * @return string The route controller class name.
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * Process the route.
     * @param ServerRequest $request The ServerRequest.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse The ClientResponse.
     * @throws RouterException if the controller class is not valid.
     */
    public function process(ServerRequest $request, ClientResponse $response): ClientResponse
    {
        if (
            !class_exists($this->controller) ||
            !method_exists($this->controller, 'invokeAction') ||
            !method_exists($this->controller, 'getResponse')
        ) {
            throw RouterException::forInvalidController($this->controller);
        }

        $controller = new $this->controller($request, $response);

        $controller->invokeAction($this->action, $this->arguments);

        return $controller->getResponse();
    }

    /**
     * Set the route arguments from a path.
     * @param string $path The path.
     * @return Route A new Route.
     */
    public function setArgumentsFromPath(string $path): static
    {
        $temp = clone $this;

        $regex = $temp->getPathRegExp();

        $temp->arguments = array_map(
            fn(string $argument): string => preg_replace($regex, $argument, $path),
            $temp->arguments
        );

        return $temp;
    }

}
