<?php

if (!defined('CORE2'))
  exit;

// Frontend binaries
require_once __DIR__.'/user/user.php';
require_once __DIR__.'/recipes/recipe.php';
require_once __DIR__.'/books/books.php';
require_once __DIR__.'/home.php';
require_once __DIR__.'/user/search.php';

if ($Controller->isAuthenticated() && $Controller->User()->isAdmin())
  require_once __DIR__.'/admin/autoload.php';
