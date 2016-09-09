<?php

    use PHPUnit\Framework\TestCase;

    use \WOK\Router\Route;
    use \WOK\Uri\Uri;
    use \WOK\Uri\Components\Path;


    class RouteTest extends TestCase {

        /**
         * Instanciate tests values
         * @note This constructor specify a default route
        **/
        public function __construct() {

            $this->route = new Route(
                ['GET', 'HEAD', 'POST'],
                '/path/to/my/resource'
            );

        }

        public function testUriMethodCall() {

            $route = clone $this->route;

            $route->setPath(new Path(
                '/sub/prefix'.(string) $route->uri->path
            ));

            $this->assertEquals('/sub/prefix/path/to/my/resource', (string) $route->uri->path);

        }

    }
