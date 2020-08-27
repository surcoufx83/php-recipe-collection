<?php

namespace Surcouf\Cookbook;

use \Ahc\Cli\Application;
use Ahc\Cli\IO\Interactor;
use \Ahc\Cli\Output\Writer;

if (!defined('CORE2'))
  exit;

$writer = new Writer;
$app = new Application('Archive-cli', 'v0.0.1');
$interactor = new Interactor;
