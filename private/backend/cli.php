<?php

namespace Surcouf\Cookbook;

use \Ahc\Cli\Application;
use Ahc\Cli\IO\Interactor;
use \Ahc\Cli\Output\Writer;

if (!defined('CORE2'))
  exit;

$writer = new Writer;
$app = new Application('Archive-cli', 'v1.0');
$interactor = new Interactor;
