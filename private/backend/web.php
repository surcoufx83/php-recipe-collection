<?php

namespace Surcouf\Cookbook;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

if (!defined('CORE2'))
  exit;

if (file_exists(DIR_BACKEND.'/conf.oauth2.php'))
    require_once DIR_BACKEND.DIRECTORY_SEPARATOR.'conf.oauth2.php';

$loader = new FilesystemLoader(DIR_FRONTEND.'/html');
$twig = new Environment($loader, array(
    'cache' => (DEBUG ? false : DIR_CACHE),
    'debug' => DEBUG
));
if (DEBUG === true)
  $twig->addExtension(new DebugExtension());

$OUT['Controller'] =& $Controller;
$OUT['Dispatcher'] = $Controller->Dispatcher();
$OUT['Config'] = $Controller->Config();
$OUT['Page'] = array(
  'Badges' => array(
    'InboxCount' => 0,
  ),
  'Breadcrumbs' => array(),
  'Heading1' => '',
  'Scripts' => array(
    'Custom' => array(),
    'FormValidator' => false,
  ),
);
$OUT['Platform'] = array();
