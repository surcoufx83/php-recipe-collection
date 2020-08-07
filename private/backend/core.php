<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE1'))
  exit;

define('CORE2',  microtime(true));

define('DEBUG', true);
define('ISCONSOLE', php_sapi_name() === 'cli');
define('ISWEB', php_sapi_name() !== 'cli');

define('DTF_SQL', 'Y-m-d H:i:s');

$NOW = new \DateTime();
$TODAY = new \DateTime($NOW->format('Y-m-d 00:00:00'));

require_once __DIR__.'/security.php';

if (DEBUG === true)
  error_reporting(E_ALL);

$start = microtime(true);
$OUT = array();
