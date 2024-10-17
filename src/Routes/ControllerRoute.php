<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Route;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

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
     * @param array $destination The route destination.
     * @param string $path The route path.
     */
    public function __construct(array $destination, string $path = '')
    {
        parent::__construct($destination, $path);

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
     * Process the route.
     *
     * @param ServerRequest $request The ServerRequest.
     * @return ClientResponse|string The ClientResponse or string response.
     *
     * @throws RouterException if the controller class or method is not valid.
     */
    public function process(ServerRequest $request, ClientResponse $response): ClientResponse|string
    {
        if (!class_exists($this->controller)) {
            throw RouterException::forInvalidController($this->controller);
        }

        if (!method_exists($this->controller, $this->action)) {
            throw RouterException::forInvalidMethod($this->controller, $this->action);
        }

        $controller = new $this->controller($request, $response);

        return $controller->{$this->action}(...$this->arguments);
    }
}
