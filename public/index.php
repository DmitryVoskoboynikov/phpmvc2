<?php

require_once('autoloader.php');

define("APP_PATH", dirname(dirname(__FILE__)));

$path = '..';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

try {
    $configuration = new Framework\Configuration(array(
        "type" => "ini"
    ));

    $configuration = $configuration->initialize();
    $configuration->parse(APP_PATH . '/application/configuration/database');

    $cache = new Framework\Cache(array(
       "type" => "memcached"
    ));
    $cache = $cache->initialize();

} catch (Exception $e)
{
    print_r($e);
}