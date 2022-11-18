<?php

ini_set('display_errors', 1);

//
if (!function_exists('create_function')) {

    function create_function($args, $code) {
        return create_function_php8($args, $code);
    }

    define('LOMBAX_PHP8_CREATE_FUNCTION_REPLACEMENT_ENABLED', true);
} else {
    define('LOMBAX_PHP8_CREATE_FUNCTION_REPLACEMENT_ENABLED', false);
}

function create_function_php8($args, $code) {

    // try to infer "byref"
    $byref = false;
    if (strpos($args, "&") !== false) {
        $byref = true;
    }

    if ($byref === true) {
        $func = function (&...$runtimeArgs) use ($args, $code, $byref) {
            return lombax_create_function_closure($args, $code, $runtimeArgs, $byref);
        };
    } else {
        $func = function (...$runtimeArgs) use ($args, $code, $byref) {
            return lombax_create_function_closure($args, $code, $runtimeArgs, $byref);
        };
    }

    return $func;
}

function lombax_create_function_closure($args, $code, $runtimeArgs, $byref = false) {
    $args = str_replace(" ", "", $args);
    $args = explode(",", $args);

    // declare the args with variable variables
    $i = 0;
    foreach ($args as $singleArg) {
        $newArg = $args[$i];
        // simple variable
        if (substr($singleArg, 0, 1) == "$") {
            $newArg = str_replace("$", "", $newArg);
            $$newArg = $runtimeArgs[$i];
            // variable passed by reference
        } else if (substr($singleArg, 0, 1) == "&") {
            if ($byref === true) {
                $newArg = str_replace("&$", "", $newArg);
                $$newArg = &$runtimeArgs[$i];
            } else {
                throw new Exception("Cannot pass variables by reference, use create_function_php8_byref instead");
            }
        } else {
            throw new Exception("create_function replacement, not managed case");
        }

        $i++;
    }

    $res = eval($code);
    return $res;
}

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

    //$router->dispatch();

    /** Templates */
    $tmp = <<<EOT
{if \$name && \$address}
    name: {echo \$name} <br />
    address: {echo \$address} <br />
{/if}
{elseif \$name}
    name: {echo \$name} <br />
{/elseif}

{foreach \$value in \$stack}
    {foreach \$item in \$value}
        item ({echo \$item_i}): {echo \$item} <br />
    {/foreach}
{/foreach}

{foreach \$value in \$empty}
    <!--never printed-->
{/foreach}
{else}
    nothing in that stack! <br />
{/else}

{macro test(\$args)}
    this item's value is: {echo \$args} <br />
{/macro}

{foreach \$value in \$stack['one']}
    {echo test(\$value)}
{/foreach}

{if true}
    in first <br />
    {if true}
        in second <br />
        {if true}
            in third <br />
        {/if}
    {/if}
{/if}

{literal}
    {echo "hello world"}
{/literal}
EOT;

    //echo $tmp;

    $data = array(
        "name" => "Chris",
        "address" => "Planet Earth!",
        "stack" => array(
            "one" => array(1, 2, 3),
            "two" => array(4, 5, 6)
        ),
        "empty" => array()
    );

    $template = new Framework\Template();
    $template->implementation = new Framework\Template\Implementation\Standard();
    $template->parse($tmp);
    $res = $template->process($data);
    echo $res;

}
catch (Exception $e)
{
    print_r($e);
}