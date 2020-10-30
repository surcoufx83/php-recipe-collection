<?php

namespace Surcouf\Cookbook\Controller\Routes\Recipe;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class RecipePublishRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;

    $recipe = $Controller->Dispatcher()->getObject();
    if (is_null($recipe) ||
        $recipe->getUserId() != $Controller->User()->getId())
      $Controller->Dispatcher()->forwardTo($Controller->getLink('private:home'));

    $recipe->setPublic(!$recipe->isPublished());
    $Controller->Dispatcher()->forwardTo($Controller->getLink('recipe:show', $recipe->getId(), $recipe->getName()));

  }

}
