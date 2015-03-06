<?php

define('SRC_PATH', dirname(__FILE__).'/../src/');
define('TEST_PATH', dirname(__FILE__).'/');

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('PhCompile\Tests', __DIR__);
