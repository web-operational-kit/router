<?php

    use PHPUnit\Framework\TestCase;

    use \WOK\Router\Collection;
    use \WOK\Router\Route;
    use \WOK\Uri\Uri;

    class CollectionTest extends TestCase {

        public function testCollection() {

            $collection = new Collection();

            $route = new Route(
                ['GET', 'HEAD', 'POST'],
                '/path/to/my/resource',
                [
                    'param' => '[a-z]+'
                ]
            );

            $collection->addRoute($route, ['controller', 'action'], 'namedRoute');

            $this->assertTrue($collection->hasRoute('namedRoute'), 'Test routes naming');
            $this->assertEquals($route, $collection->getRoute('namedRoute'), 'Test getting routes (must return a route object)');
            /// sub/project

        }

    }
