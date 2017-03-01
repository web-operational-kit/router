# Router

This library provides a leightweight router based on regular expressions.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3515ec20-1fc4-4603-851b-0aeaf217e8c4/big.png)](https://insight.sensiolabs.com/projects/3515ec20-1fc4-4603-851b-0aeaf217e8c4)

**Diclaimer** : This component is part of the WOK (Web Operational Kit) framework.
It however can be used as a standalone library.

## Install

It is recommanded to install that component as a dependency using [Composer](https://getcomposer.org/) :

    composer require wok/router


You're also free to get it with [git](https://git-scm.com/) or by [direct download](https://github.com/web-operational-kit/router/archive/master.zip) while this package has no dependencies.

    git clone https://github.com/web-operational-kit/router.git


## Features

As any other router, these features are available :

- Routing with a method and a URI (see the [usage](#usage) section)
- Route Parameters replacing and matching
- Manipulating routes definition afterwards
- Retrieving routes meta data independently
- Routes collection manipulation

**Note :**
Some features will not be implemented for now because of the wish of simplicity (and independance) of that library.

This is why the dispatcher does not execute any function. It only returns the first matching route with the information that would be needed.

Make your own opinion :)

## Basic usage

``` php
use \WOK\Router\Route;
use \WOK\Router\Collection;

// First instanciate a collection
$collection = new Collection();

$collection = new Collection();
$collection->addRoute(
    new Route(
        ['POST', 'GET', 'HEAD'],// Define the accepted HTTP methods
        '/path/to/the/{resource}', // Define the route URI
        [ // Define the URI parameters
            'resource'  => '[a-z0-9\-]+'
        ]
    ),
    'Controller::action', // Define the target (function name, class, object, array, Closure, ...)
    'Controller->Action' // Define the route name
);

// Define many other routes ...


// Retrieve the first matching route
try {

    $route = $collection->match('GET', '/path/to/the/resource-file-name');

}

// No route match the current request
catch(\DomainException $e) {

    $route = (object) array(
        'name'          => 'Controller->pageNotFound',
        'action'        => ['Controller', 'pageNotFound'],
        'parameters'    => []
    );

}

// Play with the route value
call_user_func_array($route->action, $route->parameters);
```

**Warning:**
To prevent any borring returned value the `Collection::match` throws a `DomainException` if no route is found.

That way, feel free to define any not found behavior
