<?php
/**
 * Date: 2019-12-26
 * Time: 10:27
 */

namespace Test;


use Moon\Routing\Route;
use Moon\Routing\RouteCollection;
use Moon\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

//    /** @var Router */
//    protected $router;

    public function testRouter()
    {
//        print(__METHOD__.PHP_EOL);
        $router = new Router([
            'namespace' => 'app\\controllers',    //support controller namespace
            'middleware' => [                     //support middleware
                'startSession',
                'verifyCSRFToken',
                'auth'
            ],
            'prefix' => 'test'                    //support prefix
        ]);

        $this->assertInstanceOf('Moon\Routing\RouteCollection', $router->getRoutes());
        $this->assertCount(0, $router->getRoutes());

        return $router;
    }

    /**
     * @depends testRouter
     *
     */
    public function testRouterAdd(Router $router)
    {
//        print(__METHOD__.PHP_EOL);

        $router->get('closure', function () {
            return $_SERVER['REQUEST_METHOD'] . ' test';
        })->name('test.get.closure');

        $router->post('closure/{id}', function ($id) {
            return $_SERVER['REQUEST_METHOD'] . ' test ' . $id;
        })->name('test.post.closure');

        $this->assertCount(2, $router->getRoutes());
        //var_dump($router->getRoutes());
    }
}