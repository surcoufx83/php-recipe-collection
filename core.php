<?php

declare(strict_types=1);

define('CORE1', microtime(true));
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(__DIR__));

define('DIR_BACKEND', realpath(__DIR__.DS.'private'.DS.'backend'));
define('DIR_CONFIG', realpath(__DIR__.DS.'config'));
define('DIR_CACHE', realpath(__DIR__.DS.'cache'));
define('DIR_ENTITIES', realpath(__DIR__.DS.'private'.DS.'entities'));
define('DIR_FRONTEND', realpath(__DIR__.DS.'private'.DS.'frontend'));
define('DIR_LOCALES', realpath(__DIR__.DS.'private'.DS.'i18n'));
define('DIR_OCRCACHE', realpath(__DIR__.DS.'cache'.DS.'ocr'));

define('DIR_PUBLIC', realpath(__DIR__.DS.'public'));
define('DIR_PUBLIC_IMAGES', realpath(__DIR__.DS.'public'.DS.'pictures'));

require_once realpath(__DIR__.DS.'vendor'.DS.'autoload.php');
require_once __DIR__.DS.'vendor'.DS.'surcoufx83'.DS.'php-i18n'.DS.'i18n.class.php';
require_once realpath(__DIR__.DS.'private'.DS.'autoload.php');
