<?php

namespace Surcouf\Cookbook;

require_once DIR_BACKEND    .'/core.php';
require_once DIR_BACKEND    .'/I18N.php';

// Functions without classes yet
require_once DIR_BACKEND    .'/conf.mysql.php';
require_once DIR_BACKEND    .'/conf.sendmail.php';

if (file_exists(DIR_BACKEND.DS.'conf.oauth2.php'))
    require_once DIR_BACKEND.DS.'conf.oauth2.php';

spl_autoload_register(function($className)
{
  $className = str_replace(__NAMESPACE__.'\\', '', $className);
  $file = DIR_ENTITIES . '/' . str_replace('\\', DS, $className) . '.php';
  if (file_exists($file))
    include_once($file);
});

require_once DIR_BACKEND    .'/core.php';

$Controller = new Controller();
$Controller->init();

if (ISWEB) {
  require_once DIR_BACKEND  .'/web.php';
  if (Controller\RoutingManager::registerRoutes()) {
    $Controller->Dispatcher()->dispatchRoute();
    exit;
  }
  if (MAINTENANCE)
    $Controller->Dispatcher()->forwardTo($Controller->getLink('maintenance'));
  $Controller->Dispatcher()->routingFailed();
} else if (ISCONSOLE) {
  require_once DIR_BACKEND  .'/cli.php';
  require_once DIR_FRONTEND .'/cli/autoload.php';
}
