<?php

namespace Moon\Routing;

/**
 * Router.
 * User: Heropoo
 * Date: 2017/8/8
 * Time: 17:33
 */
class Router
{
    /**
     * All of the verbs supported by the router.
     *
     * @var array
     */
    const VERBS = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Router constructor.
     * @param RouteCollection|null $routes
     * @param array $attributes
     */
    public function __construct(RouteCollection $routes = null, array $attributes = [])
    {
        $this->routes = $routes ? $routes : new RouteCollection();
        $this->attributes = $attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $path
     * @param string|\Closure $action
     * @return Route
     */
    public function head($path, $action)
    {
        return $this->addRoute($path, 'HEAD', $action);
    }

    /**
     * @param string $path
     * @param string|\Closure $action
     * @return Route
     */
    public function get($path, $action)
    {
        return $this->addRoute($path, 'GET', $action);
    }

    /**
     * @param string $path
     * @param string|\Closure $action
     * @return Route
     */
    public function post($path, $action)
    {
        return $this->addRoute($path, 'POST', $action);
    }

    /**
     * @param string $path
     * @param string|\Closure $action
     * @return Route
     */
    public function put($path, $action)
    {
        return $this->addRoute($path, 'PUT', $action);
    }

    /**
     * @param string $path
     * @param string|\Closure $action
     * @return Route
     */
    public function patch($path, $action)
    {
        return $this->addRoute($path, 'PATCH', $action);
    }

    /**
     * @param string $path
     * @param string|\Closure $action
     * @return Route
     */
    public function delete($path, $action)
    {
        return $this->addRoute($path, 'DELETE', $action);
    }

    /**
     * @param string $path
     * @param string|\Closure $action
     * @return Route
     */
    public function options($path, $action)
    {
        return $this->addRoute($path, 'OPTIONS', $action);
    }

    /**
     * @param string|array $methods
     * @param string $path
     * @param string|\Closure $action
     * @return Route
     */
    public function match($methods, $path, $action)
    {
        return $this->addRoute($path, $methods, $action);
    }

    /**
     * @param string $path
     * @param string|\Closure $action
     * @return Route
     */
    public function any($path, $action)
    {
        return $this->addRoute($path, static::VERBS, $action);
    }

    /**
     * @param array $attributes
     * @param \Closure $callback
     */
    public function group($attributes, \Closure $callback)
    {
        $router = clone $this;
        $router->mergeAttributes($attributes);
        $callback($router);
        unset($router);
    }

    /**
     * @param string $path
     * @param string|array $methods
     * @param string|\Closure $action
     * @return Route
     */
    public function createRoute($path, $methods, $action)
    {
        if (isset($this->attributes['prefix'])) {
            $path = rtrim($this->attributes['prefix'] . '/' . trim($path, '/'), '/');
        }

        if (!$action instanceof \Closure && isset($this->attributes['namespace'])) {
            $action = '\\' . trim($this->attributes['namespace'] . '\\' . trim($action, '\\'), '\\');
        }

        $route = new Route([
            'path' => $path,
            'methods' => (array)$methods,
            'action' => $action
        ]);

        if (isset($this->attributes['middleware'])) {
            $route->middleware($this->attributes['middleware']);
        }

        return $route;
    }

    /**
     * @param string $path
     * @param string|array $methods
     * @param string|\Closure $action
     * @return Route
     */
    public function addRoute($path, $methods, $action)
    {
        $route = $this->createRoute($path, $methods, $action);
        //$name = md5(implode('.', $route->getMethods()) . '.' . $route->getPath());
        $name = implode('.', $route->getMethods()) . ':' . $route->getPath();
        $route->name($name);
        $this->routes->add($name, $route);
        return $route;
    }

    /**
     * @param array $attributes
     */
    protected function mergeAttributes($attributes)
    {
        if (isset($attributes['prefix'])) {
            $prefix = isset($this->attributes['prefix']) ? $this->attributes['prefix'] : '/';
            $prefix = $prefix . '/' . trim($attributes['prefix'], '/');
            $this->attributes['prefix'] = $prefix;
        }

        if (isset($attributes['namespace'])) {
            $namespace = isset($this->attributes['namespace']) ? $this->attributes['namespace'] : '\\';
            $namespace = $namespace . '\\' . trim($attributes['namespace'], '\\');
            $this->attributes['namespace'] = $namespace;
        }

        if (isset($attributes['middleware'])) {
            $middleware = isset($this->attributes['middleware']) ? $this->attributes['middleware'] : [];
            $middleware = array_merge($middleware, (array)$attributes['middleware']);
            $middleware = array_unique($middleware);
            $this->attributes['middleware'] = $middleware;
        }
    }

    /**
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $name
     * @return null|Route
     */
    public function getRoute($name)
    {
        return $this->routes->get($name);
    }
}