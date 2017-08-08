<?php
/**
 * Created by PhpStorm.
 * User: ttt
 * Date: 2017/8/8
 * Time: 21:04
 */

namespace Moon\Routing;

/**
 * Class Route
 * @package Moon\Routing
 */
class Route extends \Symfony\Component\Routing\Route
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $middleware = [];

    /**
     * @param string $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|array $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        $this->middleware = array_merge($this->middleware, (array)$middleware);
        $this->middleware = array_unique($this->middleware);
        return $this;
    }

    /**
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
}