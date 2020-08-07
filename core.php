<?php

declare(strict_types=1);

define('CORE1', microtime(true));
define('ROOT', realpath(__DIR__));

define('DIR_BACKEND', realpath(__DIR__.'/private/backend'));
define('DIR_CACHE', realpath(__DIR__.'/cache'));
define('DIR_ENTITIES', realpath(__DIR__.'/private/entities'));
define('DIR_FRONTEND', realpath(__DIR__.'/private/frontend'));
define('DIR_LOCALES', realpath(__DIR__.'/private/i18n'));
define('DIR_OCRCACHE', realpath(__DIR__.'/cache/ocr'));

define('DIR_PUBLIC', realpath(__DIR__.'/public'));
define('DIR_PUBLIC_IMAGES', realpath(__DIR__.'/public/pictures'));

require_once realpath(__DIR__.'/vendor/autoload.php');
require_once __DIR__.'/vendor/surcoufx83/php-i18n/i18n.class.php';

require_once realpath(__DIR__.'/private/autoload.php');
