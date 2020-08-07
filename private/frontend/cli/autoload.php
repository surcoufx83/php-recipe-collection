<?php

if (!defined('CORE2'))
  exit;

// CLI Frontend binaries
require_once __DIR__.'/general/maintenance-on.php';
require_once __DIR__.'/general/maintenance-off.php';
require_once __DIR__.'/mounts/list.php';
require_once __DIR__.'/mounts/scan.php';
require_once __DIR__.'/ocr/list-missing.php';
require_once __DIR__.'/ocr/purge-images.php';
require_once __DIR__.'/setup/benchmark.php';
require_once __DIR__.'/setup/benchmark-algos.php';
require_once __DIR__.'/user/grant.php';
require_once __DIR__.'/user/list.php';
