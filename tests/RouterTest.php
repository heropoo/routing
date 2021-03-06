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
    public function testRouter()
    {
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
     * @param Router $router
     *
     */
    public function testRouterAdd(Router $router)
    {
        $router->get('', function () {
            return 'Welcome tester ＼( ^▽^ )／';
        }, 'test.get.index');

        $router->post('user/create', function () {
            return $_POST;
        }, 'test.create.user');

        $router->get('user/{id}', function ($id) {
            return 'Test get user ' . $id;
        }, 'test.get.user');

        $router->put('update/{id}', function ($id) {
            return 'Test update user ' . $id . ":" . var_export($_POST, true);
        }, 'test.put.user');

        $router->group(['middleware' => ['checkAuth']], function (Router $router) {
            $router->delete('delete/{id}', function ($id) {
                return 'Test delete user ' . $id;
            });
        });

        $this->assertCount(5, $router->getRoutes());

        $this->assertEquals($router->getRoute('test.create.user')->getPath(), '/test/user/create');

        foreach ($router->getRoutes() as $route) {
            /** @var Route $route */
            if (in_array('DELETE', $route->getMethods())) {
                $this->assertEquals($route->getName(), 'DELETE:/test/delete/{id}');
                $this->assertEquals($route->getMiddleware()[3], 'checkAuth');
            }
        }

        return $router;
    }

    /**
     * @depends testRouterAdd
     *
     * @param Router $router
     */
    public function testMatch(Router $router)
    {
        $res = $router->dispatch('/test/', 'GET'); //TODO /test
        //var_dump($res);
        $this->assertInstanceOf('Moon\Routing\Route', $res['route']);
        $this->assertEquals('test.get.index', $res['route']->getName());
        $this->assertEquals(['GET'], $res['route']->getMethods());
        $this->assertEquals('/test/', $res['route']->getPath()); //TODO /test
        $this->assertEquals('tree', $res['match_mode']);
        $this->assertEquals([], $res['params']);


        $res = $router->dispatch('/test/delete/123', 'DELETE');
//        var_dump($res);
        $this->assertInstanceOf('Moon\Routing\Route', $res['route']);
        $this->assertEquals('DELETE:/test/delete/{id}', $res['route']->getName());
        $this->assertEquals(['DELETE'], $res['route']->getMethods());
        $this->assertEquals('/test/delete/{id}', $res['route']->getPath());
        $this->assertEquals('regex', $res['match_mode']);
        $this->assertEquals(['id' => '123'], $res['params']);
        $this->assertEquals('checkAuth', $res['route']->getMiddleware()[3]);
    }
}