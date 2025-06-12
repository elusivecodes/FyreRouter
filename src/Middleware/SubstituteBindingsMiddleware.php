<?php
declare(strict_types=1);

namespace Fyre\Router\Middleware;

use Closure;
use Fyre\Container\Container;
use Fyre\Entity\Entity;
use Fyre\Entity\EntityLocator;
use Fyre\Error\Exceptions\NotFoundException;
use Fyre\Middleware\Middleware;
use Fyre\ORM\ModelRegistry;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use ReflectionNamedType;

use function is_subclass_of;

/**
 * SubstituteBindingsMiddleware
 */
class SubstituteBindingsMiddleware extends Middleware
{
    /**
     * New SubstituteBindingsMiddleware constructor.
     *
     * @param Container $container The Container.
     */
    public function __construct(
        protected Container $container,
        protected ModelRegistry $modelRegistry,
        protected EntityLocator $entityLocator
    ) {}

    /**
     * Handle a ServerRequest.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param Closure $next The next handler.
     * @return ClientResponse The ClientResponse.
     */
    public function handle(ServerRequest $request, Closure $next): ClientResponse
    {
        $route = $request->getParam('route');

        if (!$route) {
            return $next($request);
        }

        $arguments = $route->getArguments();

        if ($arguments === []) {
            return $next($request);
        }

        $params = $route->getParameters();
        $fields = $route->getBindingFields();

        $parent = null;

        foreach ($params as $param) {
            $name = $param->getName();

            $arguments[$name] ??= null;

            $type = $param->getType();

            if (!($type instanceof ReflectionNamedType)) {
                continue;
            }

            $typeName = $type->getName();

            if (!is_subclass_of($typeName, Entity::class)) {
                continue;
            }

            if ($arguments[$name] !== null) {
                $alias = $this->entityLocator->findAlias($typeName);
                $Model = $this->modelRegistry->use($alias);
                $field = $fields[$name] ?? $Model->getRouteKey();

                $entity = $Model->resolveRouteBinding($arguments[$name], $field, $parent);
            } else {
                $entity = null;
            }

            if (!$entity && !$type->allowsNull()) {
                throw new NotFoundException();
            }

            if ($entity) {
                $parent = $entity;
            }

            $arguments[$name] = $entity;
        }

        $route->setArguments($arguments);

        return $next($request);
    }
}
