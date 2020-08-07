<?php

define('CORE2', true);

define('DIR_PUBLIC', realpath(__DIR__.'/../public'));
define('DIR_PUBLIC_IMAGES', realpath(__DIR__.'/../public/pictures'));

function loader($class)
{
    $file = $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('loader');
