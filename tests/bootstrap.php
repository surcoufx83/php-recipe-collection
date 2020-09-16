<?php

define('CORE2', true);

define('DIR_PUBLIC', realpath(__DIR__.'/../public'));
define('DIR_PUBLIC_IMAGES', realpath(__DIR__.'/../public/pictures'));
define('ISWEB', true);

$_SERVER['HTTP_HOST'] = 'foo.bar';
$_SERVER['REQUEST_SCHEME'] = 'https';

function loader($class)
{
    $file = $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('loader');
