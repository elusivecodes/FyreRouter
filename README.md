# FyreRouter

**FyreRouter** is a free, open-source URL routing library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)
- [Routes](#routes)
    - [Closure Routes](#closure-routes)
    - [Controller Routes](#controller-routes)
    - [Redirect Routes](#redirect-routes)
- [Router Middleware](#router-middleware)



## Installation

**Using Composer**

```
composer require fyre/router
```

In PHP:

```php
use Fyre\Router\Router;
```


## Methods

**Add Placeholer**

Add a placeholder.

- `$placeholder` is a string representing the placeholder.
- `$pattern` is a string representing the regular expression.

```php
Router::addPlaceholder($placeholder, $pattern);
```

**Clear**

Clear all routes and aliases.

```php
Router::clear();
```

**Connect**

Connect a route.

- `$path` is a string representing the route path, and can include placeholders or regular expressions (that will be passed to the destination).
- `$destination` can be either a string representing the destination, an array containing the class name and method or a *Closure*.
- `$options` is an array containing configuration options.
    - `as` is a string representing the route alias, and will default to *null*.
    - `middleware` is an array of middleware to be applied to the route, and will default to *[]*.
    - `method` is an array of strings representing the matching methods, and will default to *[]*.
    - `redirect` is a boolean indicating whether the route is a redirect, and will default to *false*.

```php
Router::connect($path, $destination, $options);
```

You can generate the following helper methods to connect specific routes.

```php
Router::delete($path, $destination, $options);
Router::get($path, $destination, $options);
Router::patch($path, $destination, $options);
Router::post($path, $destination, $options);
Router::put($path, $destination, $options);
Router::redirect($path, $destination, $options);
```

See the [Routes](#routes) section for supported destination formats.

You can also pass additional arguments to the middleware by appending a colon followed by a comma-separated list of arguments to the string. You can use route placeholders as arguments by referencing the route placeholder index surrounded by curly braces.

```php
Router::get('test/(.*)', 'test', ['middleware' => 'alias:test,{1}']);
```

**Get Base Uri**

Get the base uri.

```php
$baseUri = Router::getBaseUri();
```

**Get Placeholders**

Get the placeholders.

```php
$placeholders = Router::getPlaceholders();
```

**Group**

Create a group of routes.

- `$options` is an array containing the group options.
    - `prefix` is a string representing the route group path prefix, and will default to *null*.
    - `as` is a string representing the route group alias prefix, and will default to *null*.
    - `middleware` is an array of middleware to be applied to the route group, and will default to *[]*.
- `$callback` is a *Closure* where routes can be defined using the prefix.

```php
Router::group($options, $callback);
```

**Load Route**

Load a route.

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).

```php
$request = Router::loadRoute($request);
```

This method will return a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests), with the `route` parameter set to the loaded route.

**Set Base Uri**

Set the base uri.

- `$baseUri` is a string representing the base uri.

```php
Router::getBaseUri($baseUri);
```

**Url**

Generate a URL for a named route.

- `$name` is a string representing the route alias.
- `$arguments` is an array containing the route arguments.
    - `?` is an array containing route query parameters.
    - `#` is a string representing the fragment component of the URI.
- `$options` is an array containing the route options.
    - `fullBase` is a boolean indicating whether to use the full base URI and will default to *false*.

```php
$url = Router::url($name, $arguments, $options)
```


## Routes

All routes extend the `Fyre\Router\Route` class, and include the following methods.

**Check Method**

Check if the route matches a test method.

- `$method` is a string representing the method to test.

```php
$checkMethod = $route->checkMethod($method);
```

**Check Path**

Check if the route matches a test path.

- `$path` is a string representing the path to test.

```php
$checkPath = $route->checkPath($path);
```

**Get Arguments**

Get the route arguments.

```php
$arguments = $route->getArguments();
```

**Get Destination**

Get the route destination.

```php
$destination = $route->getDestination();
```

**Get Middleware**

Get the route middleware.

```php
$middleware = $route->getMiddleware();
```

**Get Path**

Get the route path.

```php
$path = $route->getPath();
```

**Process**

Process the route.

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$response` is a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

```php
$response = $route->process($request, $response);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

**Set Arguments From Path**

Set the route arguments from a path.

- `$path` is a string representing the path.

```php
$newRoute = $route->setArgumentsFromPath($path);
```

**Set Methods**

- `$methods` is an array containing the route methods.

```php
$newRoute = $route->setMethods($methods);
```

**Set Middleware**

- `$middleware` is an array containing the route middleware.

```php
$newRoute = $route->setMiddleware($middleware);
```


### Closure Routes

```php
use Fyre\Router\Routes\ClosureRoute;
```

- `$destination` is a *Closure*.
- `$path` is a string representing the route path, and will default to "".

```php
$route = new ClosureRoute($destination, $path);
```

The `$destination` should be expressed in the following format:

```php
$destination = function(...$args) {
    return $response;
};
```

You can also use custom [*Entity*](https://github.com/elusivecodes/FyreEntities) types for your arguments, where the entity will be looked up automatically using the path parameter via the [*Model*](https://github.com/elusivecodes/FyreORM#models).

```php
use App\Entities\Item;

Router::connect('/items/(:num:)', function(Item $item) {
    return $response;
});
```


### Controller Routes

```php
use Fyre\Router\Routes\ControllerRoute;
```

- `$destination` is an array containing the controller class name and method.
- `$path` is a string representing the route path, and will default to "".

```php
$route = new ControllerRoute($destination, $path);
```

The `$destination` can be expressed in the following formats:

```php
$destination = [MyClass::class]; // defaults to index method
$destination = [MyClass::class, 'method'];
```

You can also use custom [*Entity*](https://github.com/elusivecodes/FyreEntities) types for your controller method arguments, where the entity will be looked up automatically using the path parameter via the [*Model*](https://github.com/elusivecodes/FyreORM#models).

```php
Router::connect('/items/(:num:)', [ItemsController::class, 'view']);

use App\Entities\Item;

class ItemsController
{
    public function view(Item $item)
    {
        return $response;
    }
}
```

**Get Action**

Get the route controller action.

```php
$action = $route->getAction();
```

**Get Controller**

Get the route controller class name.

```php
$controller = $route->getController();
```


### Redirect Routes

```php
use Fyre\Router\Routes\RedirectRoute;
```

- `$destination` is a string representing the destination.
- `$path` is a string representing the route path, and will default to "".

```php
$route = new RedirectRoute($destination, $path);
```

The `$destination` can be expressed in the following formats:

```php
$destination = 'https://test.com/';
$destination = 'https://test.com/$1';
```


## Router Middleware

```php
use Fyre\Router\Middleware\RouterMiddleware;
```

```php
$middleware = new RouterMiddleware();
```

**Process**

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$handler` is a [*RequestHandler*](https://github.com/elusivecodes/FyreMiddleware#request-handlers).

```php
$response = $middleware->process($request, $handler);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).