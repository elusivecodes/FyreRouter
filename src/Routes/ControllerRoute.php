<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use
    Fyre\Router\Route,
    Fyre\Router\Router,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest,
    RuntimeException;

use function
    array_map,
    array_shift,
    class_exists,
    explode,
    preg_replace;

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

        $this->controller = array_shift($destination);
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
     * @throws RuntimeException if the controller class does not exist.
     */
    public function process(ServerRequest $request, ClientResponse $response): ClientResponse
    {
        if (!class_exists($this->controller)) {
            throw new RuntimeException('Invalid controller class: '.$this->controller);
        }

        $controller = new $this->controller($request, $response);
        $controller->invokeAction($this->action, $this->arguments);

        return $controller->getResponse();
    }

    /**
     * Set the route arguments from a path.
     * @param string $path The path.
     * @return Route The Route.
     */
    public function setArgumentsFromPath(string $path): static
    {
        $regex = $this->getPathRegExp();

        $this->arguments = array_map(
            fn(string $argument): string => preg_replace($regex, $argument, $path),
            $this->arguments
        );

        return $this;
    }

}
