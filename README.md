# routing
A simple and fast route.Use tree structure storage, fast matching! 一个简单快速的路由，使用树形结构存储匹配更快！

[中文说明](./README_CN.md)

[![Latest Stable Version](https://poser.pugx.org/heropoo/routing/v/stable)](https://packagist.org/packages/heropoo/routing)
[![Total Downloads](https://poser.pugx.org/heropoo/routing/downloads)](https://packagist.org/packages/heropoo/routing)
[![License](https://poser.pugx.org/heropoo/routing/license)](https://packagist.org/packages/heropoo/routing)

## install
To install it via `composer`
```sh
composer require heropoo/routing
```

## feature
- Support restful style route
- Support route group and add some attributes (like namespace,middleware,prefix..)
- Support route params and limit param's type
- Support regex
- Use tree structure storage, fast matching! 

## example:
```php
<?php

require_once './vendor/autoload.php';

use Moon\Routing\Router;
use Moon\Routing\UrlMatchException;

$router = new Router([
    'namespace'=>'app\\controllers',    //support controller namespace
    'middleware'=>[                     //support middleware
        'startSession',
        'verifyCSRFToken',
        'auth'
    ],
    'prefix'=>''                        //support prefix
]);

// action also can be a Closure
$router->get('/', function(){
    return 'Welcome ＼( ^▽^ )／';
});

//route parameter
$router->get('/hello/{name}', function($name){ // auto pick route param to Closure 
    return 'Hello '.$name;
});

$router->get('/login', 'UserController::login', 'login'); // name your route
$router->post('login', 'UserController::post_login');

//use route group
$router->group(['prefix'=>'user'], function(Router $router){
    $router->post('delete/{id:\d+}', 'UserController::delete'); // {param:type pattern}
});

// match GET or POST request method
$router->match(['get', 'post'], '/api', 'ApiController::index');

// match all request method
$router->any('/other', 'ApiController::other');

// get all routes
var_dump($router->getRoutes());

/**
 * match request
 * @param string $path Request path, eg： /user/list
 * @param string $method Request method, 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS''GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'
 * @return array If not matched throw a UrlMatchException
 * return [
 *   'route' => $route,  // Route
 *   'params' => $params // array
 * ];
 *
 */
$res = $router->dispatch($path, $method);

var_dump($res);

```

Now use matched result to handle your controller's method or Closure! ＼( ^▽^ )／

## Sponsor
<div style="width: 200px; height: 200px">
<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="263" height="147" viewBox="0 0 263 147">
  <defs>
    <linearGradient id="linear-gradient" x1="54.4568" y1="122.5936" x2="251.779" y2="10.2057" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#00adee"/>
      <stop offset="1" stop-color="#9f76a6"/>
    </linearGradient>
    <linearGradient id="linear-gradient-2" x1="80.247" y1="38.7607" x2="241.2622" y2="10.9511" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#ec037c"/>
      <stop offset="1" stop-color="#9f76a6"/>
    </linearGradient>
    <linearGradient id="linear-gradient-3" x1="75.7205" y1="33.5582" x2="127.8253" y2="123.9392" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#ec037c"/>
      <stop offset="1" stop-color="#5c2d90"/>
    </linearGradient>
    <linearGradient id="linear-gradient-4" x1="7.4647" y1="44.578" x2="129.4543" y2="125.0813" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#44c7f4"/>
      <stop offset="1" stop-color="#5c2d90"/>
    </linearGradient>
  </defs>
  <g>
    <path d="M261.1839,10.3622a9.6784,9.6784,0,0,0-14.7448-8.2463l0-.0006L20.6942,136.0746h0a4.6974,4.6974,0,1,0,4.1326,8.4348h0q0.09-.0467.1784-0.097l230.5273-125.25a9.653,9.653,0,0,0,1.1508-.6253l0.0332-.018-0.0014-.0023A9.6682,9.6682,0,0,0,261.1839,10.3622Z" fill="url(#linear-gradient)"/>
    <path d="M261.1839,10.3622A9.6782,9.6782,0,0,0,251.5057.684q-0.2747,0-.5456.0157-0.25.0143-.4975,0.041L76.7981,25.4187A13.7347,13.7347,0,1,0,83.5355,51.983L252.8044,19.9512a9.6363,9.6363,0,0,0,1.0358-.196l0.02-.0039,0-.0008A9.6811,9.6811,0,0,0,261.1839,10.3622Z" fill="url(#linear-gradient-2)"/>
    <path d="M145.2028,123.63a17.2372,17.2372,0,0,0-3.0637-9.4254L91.3045,32.3521A13.7366,13.7366,0,0,0,66.1132,42.9507h0a13.6332,13.6332,0,0,0,1.043,2.4984s45.2334,86.37,45.5824,86.9979q0.3089,0.556.6567,1.0861h0A17.32,17.32,0,0,0,145.2028,123.63Z" fill="url(#linear-gradient-3)"/>
    <path d="M145.2028,123.63a17.2979,17.2979,0,0,0-7.63-13.9419h0a17.3061,17.3061,0,0,0-2.6994-1.4911L9.5484,38.9679a6.064,6.064,0,0,0-6.5074,10.187l114.3963,88.704A17.3191,17.3191,0,0,0,145.2028,123.63Z" fill="url(#linear-gradient-4)"/>
    <g>
      <rect x="69" y="51" width="70" height="70"/>
      <g>
        <rect x="75.038" y="107.8746" width="26.2498" height="4.375" fill="#fff"/>
        <g>
          <path d="M74.7429,69.4315L76.78,67.5082a2.31,2.31,0,0,0,1.7929,1.0594A1.33,1.33,0,0,0,79.8607,66.97V59.75h3.1456v7.2366a4.2386,4.2386,0,0,1-1.1246,3.2108,4.2989,4.2989,0,0,1-3.1293,1.1572A4.6592,4.6592,0,0,1,74.7429,69.4315Z" fill="#fff"/>
          <path d="M83.7394,59.75h9.1761v2.673H86.8688v1.744H92.345v2.4937H86.8688V68.47H92.997v2.6893H83.7394V59.75Z" fill="#fff"/>
          <path d="M97.049,62.5208H93.6426V59.75h9.9911v2.7708h-3.4227v8.6383H97.049V62.5208Z" fill="#fff"/>
          <path d="M75.0363,73.8257h5.8511A4.2728,4.2728,0,0,1,84,74.8363a2.5675,2.5675,0,0,1,.7335,1.858v0.0326a2.6407,2.6407,0,0,1-1.76,2.5425,2.7686,2.7686,0,0,1,2.2655,2.7871v0.0326c0,1.9558-1.5973,3.1456-4.3191,3.1456H75.0363V73.8257Zm6.5846,3.5206c0-.6357-0.5052-0.9779-1.4343-0.9779h-2.07v2.0047h1.9884c0.9616,0,1.5158-.326,1.5158-0.9942V77.3463ZM80.5289,80.59H78.1166v2.1025h2.4448c0.9779,0,1.5158-.3749,1.5158-1.0431V81.6165C82.0773,80.9972,81.5883,80.59,80.5289,80.59Z" fill="#fff"/>
          <path d="M85.7116,73.8257h5.3949a5.0512,5.0512,0,0,1,3.7161,1.2224,3.5623,3.5623,0,0,1,1.01,2.6567v0.0326a3.6146,3.6146,0,0,1-2.3469,3.5205l2.7218,3.9769H92.5733l-2.2981-3.4553H88.8735v3.4553H85.7116V73.8257Zm5.2644,5.4764a1.433,1.433,0,0,0,1.6951-1.3528V77.9167c0-.9128-0.6682-1.3691-1.7114-1.3691H88.8735v2.7545H90.976Z" fill="#fff"/>
          <path d="M99.5324,73.7443H102.58l4.8571,11.4905h-3.39l-0.815-2.0536H98.8153L98,85.2348H94.6917Zm2.7707,6.9758-1.2712-3.2271L99.7443,80.72h2.5589Z" fill="#fff"/>
          <path d="M107.8117,73.8257h3.1619V85.2348h-3.1619V73.8257Z" fill="#fff"/>
          <path d="M111.7558,73.8257h2.95l4.694,6.0306V73.8257h3.1294V85.2348h-2.7545l-4.89-6.2587v6.2587h-3.1293V73.8257Z" fill="#fff"/>
          <path d="M122.7274,83.54l1.76-2.1025a5.9106,5.9106,0,0,0,3.7,1.3691c0.8638,0,1.32-.2934,1.32-0.7824V81.9914c0-.489-0.3749-0.7335-1.9395-1.1084-2.4285-.5541-4.3029-1.2387-4.3029-3.5694V77.2811c0-2.1188,1.6788-3.6509,4.417-3.6509a7.1807,7.1807,0,0,1,4.694,1.5158l-1.5809,2.2329a5.6006,5.6006,0,0,0-3.1946-1.1246c-0.766,0-1.1409.31-1.1409,0.7334V77.02c0,0.5216.3912,0.75,1.9884,1.1083,2.6077,0.57,4.2377,1.418,4.2377,3.5531v0.0326c0,2.3307-1.8418,3.7161-4.6126,3.7161A7.9992,7.9992,0,0,1,122.7274,83.54Z" fill="#fff"/>
        </g>
      </g>
    </g>
  </g>
</svg>

</div>