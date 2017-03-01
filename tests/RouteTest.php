<?php

    use PHPUnit\Framework\TestCase;

    use \WOK\Router\Route;
    use \WOK\Uri\Uri;
    use \WOK\Uri\Components\Path;

    class RouteTest extends TestCase {

        const ROUTE_METHODS = ['GET','POST', 'HEAD'];
        const ROUTE_URI     = '/path/to/the/{resource}';
        const ROUTE_PARAMS  = ['resource' => '[a-z0-9\-]+'];

        const REQUEST_PARAMS  = ['resource' => 'file-name'];
        const REQUEST_URI     = '/path/to/the/file-name';


        /**
         * Test Route Uri manipulation
         * ---
        **/
        public function testUriMethodCall() {

            $route = $this->_getRouteInstance();

            $route->setPath(new Path(
                '/sub/prefix'.(string) $route->uri->path
            ));

            $this->assertEquals('/sub/prefix'. self::ROUTE_URI, (string) $route->uri->path);

        }

        /**
         * Test Route Url retrieving
         * ---
        **/
        public function testGetUrlWithParameters() {

            $route = $this->_getRouteInstance();

            $url = $route->getUrl(self::REQUEST_PARAMS);

            $this->assertEquals(self::REQUEST_URI, $url, Route::class.'::getUrl must return a well formated string including parameters');

        }


        /**
         * Generate a route instance
         * ---
        **/
        public function _getRouteInstance() {

            return new Route(
                self::ROUTE_METHODS,
                self::ROUTE_URI,
                self::ROUTE_PARAMS
            );

        }

    }
