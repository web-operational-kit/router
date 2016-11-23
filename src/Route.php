<?php

    namespace WOK\Router;

    use \WOK\Uri\Uri;

    /**
     * The Route class provide an interface
     * to manipulate a route meta informations.
    **/
    class Route {

        /**
         * @var array   $methods            Route accepted methods
        **/
        protected $methods      = array();

        /**
         * @var Uri      $uri               Route Uri component
        **/
        protected $uri;

        /**
         * @var array    $parameters        Route parameters (as [$key=>$pattern])
        **/
        protected $parameters   = array();

        /**
         * Instanciate a new route
         * @param   string|array       $methods            Route accepted methods
         * @param   string             $pattern            Route pattern
         * @param   array              $parameters         Route patter parameters
         *
         * @note    The $methods parameters accept ['HTTP'] in order to stipulate any method.
         *          An empty array also specify that any method is accepted
         *
         * @note    Parameters must be defined as [$name => $regexp] to match {$name} within
         *          the specified route pattern.
        **/
        public function __construct($methods = array(), $pattern, array $parameters = array()) {

            if(!is_array($methods)) {
                $methods = explode('|', $methods);
            }

            // Uppercase methods
            $methods = array_map(function($method) {
                return mb_strtoupper($method);
            }, $methods);

            // Replace `HTTP` by full HTTP methods list
            if(($http = array_search('HTTP', $methods)) !== false) {
                array_splice($methods, $http, 1, array(
                    'GET', 'HEAD', 'POST', 'PUT', 'DELETE',
                    'TRACE', 'OPTIONS', 'CONNECT', 'PATCH'
                ));
            }

            $this->methods      = $methods;
            $this->uri          = Uri::createFromString($pattern);
            $this->parameters   = $parameters;

        }


        /**
         * Get the route URI object
        **/
        public function getUri() {

            return $this->uri;

        }


        /**
         * Define the route URI
         * @param     Uri     $uri         A Uri object
        **/
        public function setUri(Uri $uri) {

            $this->uri = $uri;

        }

        /**
         * Define the route URI within a route copy
         * @param     Uri     $uri         A Uri object
        **/
        public function withUri(Uri $uri) {

            $route = clone $route;
            $route->setUri($uri);

            return $route;

        }


        /**
         * Check if a methods has been defined
        **/
        public function hasMethod($method) {

            $method = mb_stroupper($method);

            return in_array($method, $this->methods);

        }


        /**
         * Get the route URL
         * @param     array         $parameters         Routes parameters values as [$key=>$value]
         * @return    string        Returns the URL where patterns parameters have been replaced
        **/
        public function getUrl(array $parameters = array()) {

            $host = $this->uri->getHost();
            $path = $this->uri->getPath();

            foreach($this->parameters as $name => $regexp) {

               if(!isset($parameters[$name]))
                   trigger_error('Missing parameter "'. $name .'"', E_USER_ERROR);

               if(!preg_match('#^'.$regexp.'$#isU', $parameters[$name]))
                   trigger_error('Parameter "'. $name .'" doesn\'t match the REGEXP', E_USER_ERROR);

               $host = mb_str_replace('{'.$name.'}', $parameters[$name], $host);
               $path = mb_str_replace('{'.$name.'}', $parameters[$name], $path);

           }

           $uri = $this->uri->withHost($host);
           $uri->setPath($path);

           return (string) $uri;

        }

        /**
         * Access route properties
         * @param   string      $property       Route property's name
        **/
        public function __get($property) {

            if(!isset($this->$property)) {
                throw new \DomaineException('Invalid route property '.$property);
            }

            return $this->$property;

        }

        /**
         * Call methods that are parts of the Uri component
         * @throws BadMethodCallException     If the method does not exist
         * @return mixed                      Returns the method returned valud
        **/
        public function __call($method, array $arguments = array()) {

            if(!method_exists($this->uri, $method)) {
                throw new \BadMethodCallException('Route::'.$method.' does not exists.');
            }

            return call_user_func_array([$this->uri, $method], $arguments);

        }


        /**
         * Cloning behavior
        **/
        public function __clone() {

            $this->uri = clone $this->uri;

        }

    }
