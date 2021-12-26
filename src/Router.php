<?php
declare(strict_types=1);

namespace Fyre\Router;

use
    Closure,
    Fyre\Router\Exceptions\RouterException;

use function
    array_filter,
    array_map,
    array_merge,
    array_pop,
    array_shift,
    array_slice,
    array_unshift,
    class_exists,
    count,
    explode,
    implode,
    in_array,
    is_string,
    lcfirst,
    method_exists,
    trim,
    preg_match,
    preg_match_all,
    preg_replace,
    preg_replace_callback,
    rawurldecode,
    rawurlencode,
    str_replace,
    str_starts_with,
    strlen,
    strtolower,
    substr,
    ucwords;

/**
 * Router
 */
abstract class Router
{

    protected static array $routes = [];

    protected static array $pathPrefixes = [];

    protected static array $namespaces = [];

    protected static string $defaultNamespace = '\\';

    protected static array|null $defaultRoute = null;

    protected static array|null $errorRoute = null;

    protected static string $delimiter = '-';

    protected static bool $autoRoute = true;

    /**
     * Add a namespace for auto routing.
     * @param string $namespace The namespace.
     * @param string $pathPrefix The path prefix.
     */
    public static function addNamespace(string $namespace, string $pathPrefix = ''): void
    {
        $namespace = static::normalizeNamespace($namespace);

        static::$namespaces[$namespace] = static::normalizePath($pathPrefix);
    }

    /**
     * Clear all routes and namespaces.
     */
    public static function clear(): void
    {
        static::$routes = [];
        static::$namespaces = [];
    }

    /**
     * Connect a route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function connect(string $path, Closure|string $destination, array $options = []): void
    {
        $options['method'] ??= [];
        $options['method'] = array_map(
            fn($method) => strtolower($method),
            (array) $options['method']
        );
        $options['redirect'] ??= false;

        $path = static::normalizePath($path);

        if (static::$pathPrefixes !== []) {
            $path = '/'.implode('/', static::$pathPrefixes).$path;
        }

        $options['path'] = $path;

        if ($options['redirect']) {
            $options['destination'] = [
                'type' => 'redirect',
                'redirect' => $destination
            ];
        } else if (is_string($destination)) {
            $options['destination'] = static::buildRoute($destination);
        } else {
            $options['destination'] = [
                'type' => 'callback',
                'callback' => $destination
            ];
        }

        static::$routes[] = $options;
    }

    /**
     * Connect a DELETE route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function delete(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'delete']);
    }

    /**
     * Find a route.
     * @param string $path The route path.
     * @param string|null $method The request method.
     */
    public static function findRoute(string $path = '/', string|null $method = null): array
    {
        $path = static::normalizePath($path);

        $method ??= $_SERVER['REQUEST_METHOD'] ?? 'get';
        $method = strtolower($method);

        if ($path === '/' && static::$defaultRoute) {
            return static::$defaultRoute;
        }

        foreach (static::$routes AS $route) {
            if ($route['method'] !== [] && !in_array($method, $route['method'])) {
                continue;
            }

            $regex = '`^'.$route['path'].'$`';

            if (!preg_match($regex, $path, $match)) {
                continue;
            }

            $destination = $route['destination'];

            switch ($destination['type']) {
                case 'callback':
                case 'redirect':
                    $destination['arguments'] = array_slice($match, 1);
                    break;
                case 'class':
                    $destination['arguments'] = array_map(
                        fn($argument) => preg_replace($regex, $argument, $path),
                        $destination['arguments']
                    );
                    break;
            }

            return $destination;
        }

        if (static::$autoRoute) {
            return static::autoLoadRoute($path);
        }

        throw RouterException::forInvalidRoute($path);
    }

    /**
     * Connect a GET route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function get(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'get']);
    }

    /**
     * Get the default route.
     * @return array|null The default route.
     */
    public static function getDefaultRoute(): array|null
    {
        return static::$defaultRoute;
    }

    /**
     * Get the error route.
     * @return array|null The error route.
     */
    public static function getErrorRoute(): array|null
    {
        return static::$errorRoute;
    }

    /**
     * Create a group of routes.
     * @param string $prefix The route prefix.
     * @param Closure $callback The callback to define routes.
     */
    public static function group(string $pathPrefix, Closure $callback): void
    {
        static::$pathPrefixes[] = trim($pathPrefix, '/');

        $callback();

        array_pop(static::$pathPrefixes);
    }

    /**
     * Connect a PATCH route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function patch(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'patch']);
    }

    /**
     * Connect a POST route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function post(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'post']);
    }

    /**
     * Connect a PUT route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function put(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'put']);
    }

    /**
     * Connect a redirect route.
     * @param string $path The route path.
     * @param string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function redirect(string $path, string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['redirect' => true]);
    }

    /**
     * Configure whether auto-routing will be used.
     * @param bool $autoRoute Whether auto-routing will be used.
     */
    public static function setAutoRoute(bool $autoRoute = true): void
    {
        static::$autoRoute = $autoRoute;
    }

    /**
     * Set the default namespace.
     * @param string $namespace The namespace.
     */
    public static function setDefaultNamespace(string $namespace): void
    {
        static::$defaultNamespace = static::normalizeNamespace($namespace);
    }

    /**
     * Set the default route.
     * @param string $destination The destination route.
     */
    public static function setDefaultRoute(string $destination): void
    {
        static::$defaultRoute = static::buildRoute($destination);
    }

    /**
     * Set the auto-routing delimiter.
     * @param string $delimiter The delimiter.
     */
    public static function setDelimiter(string $delimiter): void
    {
        static::$delimiter = $delimiter;
    }

    /**
     * Set the error route.
     * @param string $destination The destination string.
     */
    public static function setErrorRoute(string $destination): void
    {
        static::$errorRoute = static::buildRoute($destination);
    }

    /**
     * Find a route path for a destination string.
     * @param string $destination The destination string.
     * @param array $arguments The route arguments.
     * @return string|null The route path.
     */
    public static function url(string $destination, array $arguments = []): string|null
    {
        $testRoute = static::buildRoute($destination);

        if ($arguments !== []) {
            $testRoute['arguments'] = $arguments;
        }

        $argumentCount = count($testRoute['arguments']);

        foreach (static::$routes AS $route) {
            if (
                $testRoute['type'] !== 'class' ||
                $testRoute['class'] !== $route['destination']['class'] ||
                $testRoute['method'] !== $route['destination']['method'] ||
                $argumentCount !== preg_match_all('/\(.*?\)/', $route['path'])
            ) {
                continue;
            }

            $index = 0;
            $url = preg_replace_callback(
                '/\(.*?\)/',
                function($matches) use ($testRoute, &$index) {
                    return $testRoute['arguments'][$index++];
                },
                $route['path']
            );

            $segments = array_map(
                fn($segment) => rawurlencode($segment),
                explode('/', $url)
            );

            return implode('/', $segments);
        }

        if (static::$autoRoute) {
            return static::autoLoadUrl($testRoute);
        }

        return null;
    }

    /**
     * Find an auto-route for a path.
     * @param string $path The route path.
     * @return array The route array.
     * @throws RouterException if the route was not found.
     */
    protected static function autoLoadRoute(string $path): array
    {
        $namespaces = static::getAllNamespaces();

        $segments = explode('/', $path);
        $segments = array_filter($segments);
        $segments[] = 'index';

        $segments = array_map(
            fn($segment) => static::classify($segment),
            $segments
        );

        $indexTest = true;

        $arguments = [];

        while ($segments !== []) {
            $method = array_pop($segments);
            $method = lcfirst($method);

            foreach ($namespaces AS $namespace => $pathPrefix) {
                $classSegments = $segments;

                if (!str_starts_with($path, $pathPrefix)) {
                    continue;
                }

                if ($pathPrefix !== '/') {
                    $prefixSegments = explode('/', $pathPrefix);
                    $prefixCount = count($prefixSegments);
                    $classSegments = array_slice($classSegments, $prefixCount - 1);
                }

                if ($classSegments === []) {
                    continue;
                }

                $classSuffix = implode('\\', $classSegments);
                $class = $namespace.$classSuffix;

                if (class_exists($class) && method_exists($class, $method)) {
                    return [
                        'type' => 'class',
                        'class' => $class,
                        'method' => $method,
                        'arguments' => $arguments
                    ];
                }
            }

            if ($indexTest) {
                $indexTest = false;
            } else {
                $segments[] = 'index';
                $indexTest = true;

                array_unshift($arguments, $method);
            }
        }

        throw RouterException::forInvalidRoute($path);
    }

    /**
     * Build an auto-route path from a route array.
     * @param array $route The route array.
     * @return string|null The route path.
     */
    protected static function autoLoadUrl(array $route): string|null
    {
        $namespaces = static::getAllNamespaces();

        $class = $route['class'];

        $segments = [];

        foreach ($namespaces AS $namespace => $pathPrefix) {
            if ($namespace === '\\') {
                continue;
            }

            $namespaceLength = strlen($namespace);
            if (substr($class, 0, $namespaceLength) !== $namespace) {
                continue;
            }

            $class = substr($class, $namespaceLength);

            if ($pathPrefix) {
                $segments = explode('/', $pathPrefix);
            }
        }

        $classSegments = explode('\\', $class);

        if ($route['method'] !== 'index') {
            $classSegments[] = $route['method'];
        }

        $classSegments = array_map(
            fn($segment) => static::slug($segment),
            $classSegments
        );

        $segments = array_merge($segments, $classSegments);
        $segments = array_filter($segments);

        $segments = array_merge($segments, $route['arguments']);

        $segments = array_map(
            fn($segment) => rawurlencode($segment),
            $segments
        );

        return '/'.implode('/', $segments);
    }

    /**
     * Build a route array from a destination string.
     * @param string $destination The destination string.
     * @return array The route array.
     */
    protected static function buildRoute(string $destination): array
    {
        $arguments = explode('/', $destination);
        $destination = array_shift($arguments);
        $destination = explode('::', $destination, 2);

        $class = array_shift($destination);
        if ($class && $class[0] !== '\\') {
            $class = static::$defaultNamespace.$class;
        }

        $method = array_shift($destination) ?? 'index';

        return [
            'type' => 'class',
            'class' => $class,
            'method' => $method,
            'arguments' => $arguments
        ];
    }

    /**
     * Conver a string as a class name.
     * @param string $string The input string.
     * @return string The class name.
     */
    protected static function classify(string $string): string
    {
        $string = str_replace(static::$delimiter, ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);

        return $string;
    }

    /**
     * Get all defined namespaces.
     * @return array The defined namespaces.
     */
    protected static function getAllNamespaces(): array
    {
        $namespaces = [
            static::$defaultNamespace => '/'
        ];

        return array_merge($namespaces, static::$namespaces);
    }

    /**
     * Normalize a namespace
     * @param string $namespace The namespace.
     * @return string The normalized namespace.
     */
    protected static function normalizeNamespace(string $namespace): string
    {
        $namespace = trim($namespace, '\\');

        return $namespace ?
            '\\'.$namespace.'\\' :
            '\\';
    }

    /**
     * Normalize a path
     * @param string $path The path.
     * @return string The normalized path.
     */
    protected static function normalizePath(string $path): string
    {
        $path = trim($path, '/');

        return '/'.rawurldecode($path);
    }

    /**
     * Convert a string as a URL slug.
     * @param string $string The input string.
     * @return string The URL slug.
     */
    protected static function slug(string $string): string
    {
        $string = lcfirst($string);
        $string = preg_replace('/[A-Z]/', ' \0', $string);
        $string = str_replace(' ', static::$delimiter, $string);

        return strtolower($string);
    }

}
