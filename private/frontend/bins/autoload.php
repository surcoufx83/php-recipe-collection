<?php

if (!defined('CORE2'))
  exit;

// Frontend binaries
require_once __DIR__.'/home.php';
require_once __DIR__.'/books.php';
require_once __DIR__.'/recipe.php';
require_once __DIR__.'/search.php';
require_once __DIR__.'/user.php';

if ($Controller->isAuthenticated() && $Controller->User()->isAdmin())
  require_once __DIR__.'/admin/autoload.php';
