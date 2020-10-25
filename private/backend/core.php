<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\Config;

if (!defined('CORE1'))
  exit;
define('CORE2',  microtime(true));

$Config = new Config();

define('DEBUG', $Config->System('DebugMode'));
define('DTF_SQL', 'Y-m-d H:i:s');
define('ISCONSOLE', php_sapi_name() === 'cli');
define('ISWEB', php_sapi_name() !== 'cli');

require_once realpath(__DIR__.DS.'security.php');

if (DEBUG === true)
  error_reporting(E_ALL);

$OUT = array();
