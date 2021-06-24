<?php

require_once('autoloader.php');

$delimiter = Framework\StringMethods::getDelimiter();
$array = Framework\ArrayMethods::clean(array("0" => "test", "1" => ""));

//print_r($array);

$inspector = new Framework\Inspector(new Application\Core());
$meta = $inspector->getClassMeta();
$meta2 = $inspector->getClassMeta();
print_r($meta);