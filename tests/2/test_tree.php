<?php
/**
 * Date: 2019-12-23
 * Time: 18:38
 */

$list = [
    '/' => '',
    '/login' => '',
    '/logout' => '',
    '/user' => '',
    '/user/{id}/edit' => '',
    '/user/{id}/delete' => '',
    '/api' => '',
    '/api/demo/create' => '',
    '/{aid}/api/demo' => '',
];

foreach ($list as $key => $value) {
    $value = function ($key) {
        $route = new stdClass();
        $route->path = $key;
        return $route;
    };
    $value = $value($key);
    $list[$key] = $value;
}

$list = array_values($list);

var_dump($list);exit;

$tree = [];

foreach ($list as $key => $route) {
    $path = $route->path;
    $pathArr = explode('/', $path);
    //var_dump($path);
    unset($pathArr[0]);
    $node = $pathArr[1];
    $tree[$node] = parseNode($pathArr, $route);
}

var_dump($tree);

function parseNode($pathArr, $route)
{
    array_values($pathArr);
    $node = array_pop($pathArr);

    if (empty($pathArr)) {
        return $route;
    } else {
        $tree[$node] = parseNode($pathArr, $route);
    }
    return $tree;
}