<?php

namespace Surcouf\Cookbook;

use Surcouf\Cookbook\Config;
use Symfony\Component\Yaml\Yaml;

spddg(__FILE__);

if (!defined('CORE1'))
  exit;
define('CORE2',  microtime(true));

if (!\file_exists(DIR_CONFIG.DS.'cbconfig.yml'))
  throw new \Exception("cbconfig.yml not found in folder config. Please check cbconfig.yml.templat for more information", 1);

$Config = new Config(Yaml::parse(file_get_contents(DIR_CONFIG.DS.'cbconfig.yml')));

define('DEBUG', $Config->System('DebugMode'));
define('DTF_SQL', 'Y-m-d H:i:s');
define('ISCONSOLE', php_sapi_name() === 'cli');
define('ISWEB', php_sapi_name() !== 'cli');

require_once realpath(__DIR__.DS.'security.php');

if (DEBUG === true)
  error_reporting(E_ALL);

$OUT = array();
