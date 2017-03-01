<?php

    namespace WOK\Router;

    use WOK\Router\Route;
    use WOK\Uri\Uri;

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

            if(!$name) {
                $name = md5(serialize($target));
            }

            $item = (object) array(
                'name'      => $name,
                'route'     => $route,
                'target'    => $target
            );

            $this->routes[$name] = $item;

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
         * @param   string          $requestMethod       Request method
         * @param   string|Uri      $requestUri          Request URI
         * @throws  Throw a DomainException if not route has matched
         * @return  StdClass    return the matching route
        **/
        public function match($requestMethod, $requestUri) {

            if(!($requestUri instanceof Uri)) {
                $requestUri = Uri::createFromString($requestUri);
            }

            $requestHost    = (string) $requestUri->getHost();
            $requestPath    = (string) $requestUri->getPath();

            foreach($this->routes as $name => $route) {

                $target     = $route->target;
                $route      = $route->route;
                $routeUri   = $route->uri;
                $routeHost  = (string) $routeUri->getHost();

                // Invalid method
                if(!empty($route->methods) && (!in_array($requestMethod, $route->methods)))
                    continue;

                // Invalid domain
                if($routeHost && $routeHost != $requestHost)
                    continue;

                // Prepare parameters
                $pattern = (string) $routeUri->getPath();
                foreach($route->parameters as $name => $regexp) {
                    $pattern = str_replace('{'.$name.'}', "(?<$name>$regexp)", $pattern);
                }

                // Invalid pattern
                if($pattern != $requestPath && !preg_match('#^'.$pattern.'$#isU', $requestPath, $parameters))
                    continue;

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
                    'action'        => $target,
                    'parameters'    => $parameters
                );

            }

            // No route has been found
            throw new \DomainException('Route not found for this request ('.$requestUri.')');

        }


        /**
         * Reset routes cursor position
         * @note This is an Iterator extension method
         * @see http://php.net/manual/en/function.rewind.php
        **/
        public function rewind() {
            reset($this->routes);
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
            return false !== current($this->routes);
        }

    }
