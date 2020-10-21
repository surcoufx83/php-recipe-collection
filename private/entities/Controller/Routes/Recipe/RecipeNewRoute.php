<?php

namespace Surcouf\Cookbook\Controller\Routes\Recipe;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;

if (!defined('CORE2'))
  exit;

class RecipeNewRoute extends Route implements RouteInterface {

  private static $template = 'recipes/new-recipe';

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;

    $units = [];
    $query = new QueryBuilder(EQueryType::qtSELECT, 'units', DB_ANY);
    $query->orderBy('unit_name');
    $result = $Controller->select($query);
    if ($result) {
      while($record = $result->fetch_assoc()) {
        $units[] = $Controller->OM()->Unit($record);
      }
    }

    $tags = [];
    $query = new QueryBuilder(EQueryType::qtSELECT, 'tags', DB_ANY);
    $query->orderBy('tag_name');
    $result = $Controller->select($query);
    if ($result) {
      while($record = $result->fetch_assoc()) {
        $tags[] = $Controller->OM()->Tag($record);
      }
    }

    parent::addScript('new-recipe-imguploader');
    parent::addValidationScript();
    parent::addToDictionary('Tags', $tags);
    parent::addToDictionary('Units', $units);
    parent::setPage('recipe:new');
    parent::setTitle($Controller->l('newRecipe_header', $Controller->User()->getFirstname()));
    return parent::render(self::$template, $response);
  }

}
