<?php

    namespace WOK\Router;

    use WOK\Router\Route;
    use WOK\HttpMessage\Request;

    /**
     * Routes collection manager
    **/
    class Collection implements \Iterator {

        /**
         * @var array   $routes         Routes collection
        **/
        protected $routes = array();

        /**
         * Register a new route
         * @param Route         $route          Route object
         * @param Callable      $target         Route targetted action
         * @param string        $name           Route name
        **/
        public function addRoute(Route $route, $target, $name = null) {

            $item = (object) array(
                'route'     => $route,
                'target'    => $target
            );

            if(!empty($name)) {
                $this->routes[$name] = $item;
            }
            else {
                $this->routes[] = $item;
            }

            return $route;

        }

        /**
         * Check if a route is available
         * @param string        $name           Route name
        **/
        public function hasRoute($name) {
            return isset($this->routes[$name]);
        }

        /**
         * Get a route referenced object
         * @param string        $name           Route name
        **/
        public function getRoute($name) {

            if(!$this->hasRoute($name)) {
                throw new \DomainException('Route "'.$name.'" not found');
            }

            return $this->routes[$name]->route;

        }

        /**
         * Remove a defined route
         * @param string        $name           Route name
        **/
        public function removeRoute($name) {

            if(!$this->hasRoute($name)) {
                unset($this->routes[$name]);
            }

        }

        /**
         * Get the route that match request
         * @param   Request     $request       Http Request interface
         * @throws  Throw a DomainException if not route has matched
         * @return  StdClass    return the matching route
        **/
        public function match(Request $request) {

            $method = $request->getMethod();
            $uri    = $reques->getUri();

            $requestMethod  = $request->getMethod();
            $requestUri     = (string) $request->getUri();
            $requestHost    = (string) $request->getUri()->getHost();
            $requestPath    = (string) $request->getUri()->getPath();

            foreach($this->routes as $name => $route) {

                $target = $route->target;
                $route  = $route->route;

                // Invalid domain
                if(!empty($host) && $host != $requestHost)
                    continue;

                // Invalid method
                if(!empty($route->methods) && (!in_array($requestHost, $route->methods)))
                    continue;

                // Prepare parameters
                $pattern = (string) $route->uri;
                foreach($route->parameters as $name => $regexp) {
                    $pattern = str_replace('{'.$name.'}', "(?<$name>$regexp)", $pattern);
                }

                // Invalid pattern
                if($pattern != $requestPath && !preg_match('#^'.$pattern.'$#isU', $requestPath, $parameters))
                    continue;

                // From now, this is a valid route
                list($controller, $method) = $target;

                // Reorder parameters according to definition order
                if(!empty($parameters)) {
                    $parameters = array_intersect_key($parameters, $route->parameters);
                    $parameters = array_merge(array_flip(array_keys($route->parameters)), $parameters);
                }
                else {
                    $parameters = array();
                }

                // Output route
                return (object) array(
                    'name'          => $name,
                    'controller'    => $controller,
                    'action'        => $method,
                    'parameters'    => $parameters
                );

            }

            // No route has been found
            throw new \DomainException('Route not found for this request');

        }

        /**
         * Alias/Shorcut of $collection->match()
         * @param   Request     $request       Http Request interface
         * @throws  Throw a DomainException if not route has matched
         * @return  StdClass    return the matching route
        **/
        public function __invoke(Request $request) {
            return $this->match($request);
        }


        /**
         * Reset routes cursor position
         * @note This is an Iterator extension method
         * @see http://php.net/manual/en/function.rewind.php
        **/
        public function rewind() {
            rewind($this->routes);
        }

        /**
         * Get the current route through iteration
         * @note This is an Iterator extension method
         * @see http://php.net/manual/en/function.current.php
        **/
        public function current() {
            return current($this->routes);
        }

        /**
         * Get the current route key
         * @note This is an Iterator extension method
         * @see http://php.net/manual/en/function.key.php
        **/
        public function key() {
            return key($this->routes);
        }

        /**
         * Advance the routes array pointer
         * @note This is an Iterator extension method
         * @see http://php.net/manual/en/function.next.php
        **/
        public function next() {
            return next($this->routes);
        }

        /**
         * Check if the current cursor position is valid
         * @note This is an Iterator extension method
        **/
        public function valid() {
            return false !== current($this->attributes);
        }

    }