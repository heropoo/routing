<?php
/**
 * Created by PhpStorm.
 * User: ttt
 * Date: 2017/8/8
 * Time: 17:41
 */

namespace Moon\Routing;

/**
 * Class Container
 * @package Moon\Routing
 */
class Container
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @param string $name
     * @param $instance
     */
    public function add($name, $instance){
        $this->instances[$name] = $instance;
    }

    /**
     * @param string $name
     * @return null|mixed
     */
    public function get($name){
        return isset($this->instances[$name]) ? $this->instances[$name] : null;
    }
}