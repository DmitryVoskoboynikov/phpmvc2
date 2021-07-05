<?php

require_once('autoloader.php');

define("APP_PATH", dirname(dirname(__FILE__)));

$path = '..';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

$path = '../controllers';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

try {
    /** Configuration */
    $configuration = new Framework\Configuration(array(
        "type" => "ini"
    ));
    Framework\Registry::set("configuration", $configuration->initialize());

    $configuration = Framework\Registry::get("configuration");
    $configuration->parse(APP_PATH . '/application/configuration/database');

    /**  Cache */
    $cache = new Framework\Cache(array(
       "type" => "memcached"
    ));
    $cache = $cache->initialize();
    $cache->connect();

    $friends = $cache->get("friends.1");
    if (empty($friends))
    {
        $cache->set("friends.1", "testFriends");
    }

    //echo $friends;

    /** Registry */

    /** Router */
    $route = new Framework\Router\Route\Simple(array(
        'pattern' => 'user/:id',
        'controller' => 'user',
        'action' => 'show'
    ));
    $route1 = new Framework\Router\Route\Simple(array(
        'pattern' => 'user/:id/:test',
        'controller' => 'user',
        'action' => 'edit'
    ));

    $router = new Framework\Router();
    $router->url = 'user/1/testText';
    $router->addRoute($route);
    $router->addRoute($route1);

    $router->dispatch();
}
catch (Exception $e)
{
    print_r($e);
}