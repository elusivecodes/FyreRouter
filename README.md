# FyreRouter

**FyreRouter** is a free, open-source URI routing library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Routes](#routes)
    - [Closure](#closure)
    - [Controller](#controller)
    - [Redirect](#redirect)
- [Middleware](#middleware)
    - [Substitute Bindings](#substitute-bindings)



## Installation

**Using Composer**

```
composer require fyre/router
```

In PHP:

```php
use Fyre\Router\Router;
```


## Basic Usage

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$modelRegistry` is a [*ModelRegistry*](https://github.com/elusivecodes/FyreORM).
- `$config` is a [*Config*](https://github.com/elusivecodes/FyreConfig).

```php
$router = new Router($container, $modelRegistry, $config);
```

The base URI will be resolved from the "*App.baseUri*" key in the [*Config*](https://github.com/elusivecodes/FyreConfig).

**Autoloading**

It is recommended to bind the *Router* to the [*Container*](https://github.com/elusivecodes/FyreContainer) as a singleton.

```php
$container->singleton(Router::class);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$router = $container->use(Router::class);
```


## Methods

**Clear**

Clear all routes and aliases.

```php
$router->clear();
```

**Connect**

Connect a route.

- `$path` is a string representing the route path, and can include placeholders (that will be passed to the destination).
- `$destination` can be either a string representing the destination, an array containing the class name and method or a *Closure*.
- `$options` is an array containing configuration options.
    - `as` is a string representing the route alias, and will default to *null*.
    - `middleware` is an array of middleware to be applied to the route, and will default to *[]*.
    - `method` is an array of strings representing the matching methods, and will default to *[]*.
    - `placeholders` is an array of regular expression placeholders, and will default to *[]*.
    - `redirect` is a boolean indicating whether the route is a redirect, and will default to *false*.

```php
$route = $router->connect($path, $destination, $options);
```

You can generate the following helper methods to connect specific routes.

```php
$router->delete($path, $destination, $options);
$router->get($path, $destination, $options);
$router->patch($path, $destination, $options);
$router->post($path, $destination, $options);
$router->put($path, $destination, $options);
$router->redirect($path, $destination, $options);
```

See the [Routes](#routes) section for supported path and destination formats.

You can also pass additional arguments to the middleware by appending a colon followed by a comma-separated list of arguments to the alias string. You can use route placeholders as arguments by referencing the route placeholder surrounded by curly braces.

```php
$router->get('test/{id}', 'test', ['middleware' => 'alias:test,{id}']);
```

**Get Base Uri**

Get the base uri.

```php
$baseUri = $router->getBaseUri();
```

**Group**

Create a group of routes.

- `$options` is an array containing the group options.
    - `prefix` is a string representing the route group path prefix, and will default to *null*.
    - `as` is a string representing the route group alias prefix, and will default to *null*.
    - `middleware` is an array of middleware to be applied to the route group, and will default to *[]*.
    - `placeholders` is an array of regular expression placeholders, and will default to *[]*.
- `$callback` is a *Closure* with the *Router* as the first argument.

```php
$router->group($options, $callback);
```

**Load Route**

Load a route.

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).

```php
$request = $router->loadRoute($request);
```

This method will return a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests), with the `route` parameter set to the loaded route.

**Url**

Generate a URL for a named route.

- `$name` is a string representing the route alias.
- `$arguments` is an array containing the route arguments, where the key is the placeholder name.
    - `?` is an array containing route query parameters.
    - `#` is a string representing the fragment component of the URI.
- `$options` is an array containing the route options.
    - `fullBase` is a boolean indicating whether to use the full base URI and will default to *false*.

```php
$url = $router->url($name, $arguments, $options)
```


## Routes

All routes extend the `Fyre\Router\Route` class, and include the following methods.

**Check Route**

Check if the route matches a test method and path.

- `$method` is a string representing the method to test.
- `$path` is a string representing the path to test.

```php
$checkRoute = $route->checkRoute($method, $pth);
```

**Get Arguments**

Get the route arguments.

```php
$arguments = $route->getArguments();
```

**Get Binding Fields**

Get the route binding fields.

```php
$bindingFields = $route->getBindingFields();
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

**Get Placeholders**

Get the route placeholders.

```php
$placeholders = $route->getPlaceholders();
```

**Handle**

Handle the route.

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$response` is a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

```php
$response = $route->handle($request, $response);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

**Set Middleware**

Set the route middleware.

- `$middleware` is an array containing the route middleware.

```php
$route->setMiddleware($middleware);
```

**Set Placeholder**

Set a route placeholder.

- `$placeholder` is a string representing the route placeholder.
- `$regex` is a string representing the placeholder regular expression.

```php
$route->setPlaceholder($placeholder, $regex);
```


### Closure

```php
use Fyre\Router\Routes\ClosureRoute;
```

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$destination` is a *Closure*.
- `$path` is a string representing the route path, and will default to "".
- `$options` is an array containing route options.
    - `middleware` is an array of middleware to be applied to the route, and will default to *[]*.
    - `method` is an array of strings representing the matching methods, and will default to *[]*.
    - `placeholders` is an array of regular expression placeholders, and will default to *[]*.

```php
$route = new ClosureRoute($container, $destination, $path, $options);
```

The `$path` and `$destination` can be expressed in the following formats:

```php
$router->get('posts', function(): string {
    return view('Posts.index');
});

$router->get('posts/{post}', function(Post $post): string {
    return view('Posts.view', ['post' => $post]);
}); 
```

Route parameter entity binding is handled by the [Substitute Bindings](#substitute-bindings) middleware.


### Controller

```php
use Fyre\Router\Routes\ControllerRoute;
```

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$destination` is an array containing the controller class name and method.
- `$path` is a string representing the route path, and will default to "".
- `$options` is an array containing route options.
    - `middleware` is an array of middleware to be applied to the route, and will default to *[]*.
    - `method` is an array of strings representing the matching methods, and will default to *[]*.
    - `placeholders` is an array of regular expression placeholders, and will default to *[]*.

```php
$route = new ControllerRoute($container, $destination, $path, $options);
```

The `$path` and `$destination` can be expressed in the following formats:

```php
$router->get('posts', [Posts::class]); // defaults to index method
$router->get('posts/{post}', [Posts::class, 'view']);
```

Route parameter entity binding is handled by the [Substitute Bindings](#substitute-bindings) middleware.

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


### Redirect

```php
use Fyre\Router\Routes\RedirectRoute;
```

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$destination` is a string representing the destination.
- `$path` is a string representing the route path, and will default to "".
- `$options` is an array containing route options.
    - `middleware` is an array of middleware to be applied to the route, and will default to *[]*.
    - `method` is an array of strings representing the matching methods, and will default to *[]*.
    - `placeholders` is an array of regular expression placeholders, and will default to *[]*.

```php
$route = new RedirectRoute($container, $destination, $path, $options);
```

The `$path` and `$destination` can be expressed in the following formats:

```php
$router->redirect('test', 'https://test.com/');
$router->redirect('test/{id}', 'https://test.com/{id}');
```


## Middleware

```php
use Fyre\Router\Middleware\RouterMiddleware;
```

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$middlewareRegistry` is a [*MiddlewareRegistry*](https://github.com/elusivecodes/FyreMiddleware).
- `$router` is a *Router*.

```php
$middleware = new RouterMiddleware($container, $middlewareRegistry, $router);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$middleware = $container->use(RouterMiddleware::class);
```

**Handle**

Handle a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$next` is a *Closure*.

```php
$response = $middleware->handle($request, $next);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).


### Substitute Bindings

```php
use Fyre\Router\Middleware\SubstituteBindingsMiddleware;
```

This middleware will automatically resolve entities from route placeholders based on the parameter types of the route destination.

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$middlewareRegistry` is a [*MiddlewareRegistry*](https://github.com/elusivecodes/FyreMiddleware).
- `$entityLocator` is an [*EntityLocator*](https://github.com/elusivecodes/FyreEntity).

```php
$middleware = new SubstituteBindingsMiddleware($container, $middlewareRegistry, $entityLocator);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$middleware = $container->use(SubstituteBindingsMiddleware::class);
```

**Handle**

Handle a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$next` is a *Closure*.

```php
$response = $middleware->handle($request, $next);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).
