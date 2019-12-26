<?php
/**
 * Date: 2019-09-06
 * Time: 18:02
 */

namespace Moon\Routing;


class RouteCollection implements \Countable, \IteratorAggregate
{
    protected $items = [];
    protected $names = [];

    public function count()
    {
        return count($this->items);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public function get($key)
    {
        return $this->items[$key];
    }

    public function add($key, $value)
    {
        $this->items[$key] = $value;
        return $this;
    }

    // todo
    public function name($name, $key)
    {
        $this->names[$key] = $name;
        return $this;
    }
}