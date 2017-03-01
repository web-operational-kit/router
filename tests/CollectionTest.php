<?php

    use PHPUnit\Framework\TestCase;

    use \WOK\Router\Collection;
    use \WOK\Router\Route;
    use \WOK\Uri\Uri;

    class CollectionTest extends TestCase {

        const ROUTE_METHODS = ['GET', 'POST', 'HEAD'];
        const ROUTE_URI     = '/path/to/the/{resource}';
        const ROUTE_PARAMS  = ['resource' => '[a-z0-9]+'];
        const ROUTE_ACTION  = ['Controller', 'Action'];
        const ROUTE_NAME    = 'Controller\Action';

        const REQUEST_METHOD = 'POST';
        const REQUEST_PARAMS = ['resource' => 'resourcename'];

        /***
         * Test routes collection manipulation
         * ---
        **/
        public function testCollection() {

            $collection = $this->_getRoutesCollection();

            $this->assertTrue($collection->hasRoute(self::ROUTE_NAME), Collection::class.'::hasRoute must tell that a route may have been defined');
            $this->assertFalse($collection->hasRoute('Unknow\Route\Name'), Collection::class.'::hasRoute must tell that a route may not been defined');

            $this->assertInstanceOf(Route::class, $collection->getRoute(self::ROUTE_NAME), Collection::class.'::getRoute must return a Route Object');

        }


        /**
         * test mathing method
        **/
        public function testMatchMethod() {

            $collection = $this->_getRoutesCollection();

            $path = self::ROUTE_URI;
            foreach(self::REQUEST_PARAMS as $key => $value) {
                $path = str_replace('{'.$key.'}', $value, $path);
            }

            $match = $collection->match(self::REQUEST_METHOD, $path);

            $this->assertInstanceOf(\StdClass::class, $match, Collection::class.'::match must return a StdClass object');
            $this->assertObjectHasAttribute('name',       $match, Collection::class.'::match return must contain a `name` attribute');
            $this->assertObjectHasAttribute('action',     $match, Collection::class.'::match return must contain an `action` attribute');
            $this->assertObjectHasAttribute('parameters', $match, Collection::class.'::match return must contain a `parameters` attribute');

        }

        /**
         * test mathing method failure
         * ---
        **/
        public function testMatchMethodFailure() {

            $this->expectException(\DomainException::class, Collection::class.'::match must throw an exception when no route is found');

            $collection = $this->_getRoutesCollection();

            // Test non transformed uri (not replaced parameters)
            $match = $collection->match(self::REQUEST_METHOD, '/this/is/a/not/foundable/path');

        }


        /**
         * retrieve a new routes collection
         * ---
        **/
        protected function _getRoutesCollection() {

            $route = new Route(
                self::ROUTE_METHODS,
                self::ROUTE_URI,
                self::ROUTE_PARAMS
            );

            $collection = new Collection();
            $collection->addRoute($route, self::ROUTE_ACTION, self::ROUTE_NAME);

            return $collection;

        }

    }
