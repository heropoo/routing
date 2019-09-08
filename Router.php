<?php
/**
 * User: Heropoo
 * Date: 2017/8/8
 * Time: 17:33
 */

namespace Moon\Routing;

/**
 * Class Router
 * @method Route get(string $path, string|\Closure $action)
 * @method Route head(string $path, string|\Closure $action)
 * @method Route post(string $path, string|\Closure $action)
 * @method Route put(string $path, string|\Closure $action)
 * @method Route patch(string $path, string|\Closure $action)
 * @method Route delete(string $path, string|\Closure $action)
 * @method Route options(string $path, string|\Closure $action)
 * @package Moon\Routing
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

    public function __call($name, $arguments)
    {
        $method = strtoupper($name);
        if (in_array($method, static::VERBS)) {
            if (count($arguments) < 2) {
                throw new \InvalidArgumentException('Too few arguments to function ' . get_class($this) . '::' . $name . '()');
            }
            $path = $arguments[0];
            $action = $arguments[1];
            return $this->addRoute($path, $method, $action);
        }
        throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $name . '()');
    }
}