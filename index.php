<?php

require_once('autoloader.php');

$configuration = new Framework\Configuration(array(
    "type" => "ini"
));

$configuration = $configuration->initialize();