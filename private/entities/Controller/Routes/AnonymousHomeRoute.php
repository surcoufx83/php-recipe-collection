<?php

namespace Surcouf\Cookbook\Controller\Routes;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\UiHelper\CarouselHelper;
use Surcouf\Cookbook\Recipe\Recipe;

if (!defined('CORE2'))
  exit;

final class AnonymousHomeRoute extends Route implements RouteInterface {

  private static $template = 'home';

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;
    parent::setPage('private:home');
    parent::setTitle($Controller->l('greetings_hello', ''));

    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipes', DB_ANY);
    $query->join('recipe_pictures',
              ['recipe_pictures', 'recipe_id', '=', 'recipes', 'recipe_id'],
              ['AND', 'recipe_pictures', 'picture_sortindex', '=', 0])
          ->select('recipe_pictures', DB_ANY)
          ->where('recipes', 'recipe_public', '=', 1)
          ->orderBy([['recipe_published', 'DESC']])
          ->limit(5);

    $result = $Controller->select($query);
    $carousel = CarouselHelper::createNew('new-recipes');
    if ($result) {
      while($record = $result->fetch_array()) {
        $recipe = $Controller->OM()->Recipe($record);
        CarouselHelper::addItem($carousel, [
          'href' => $Controller->getLink('recipe:show', $recipe->getId(), $recipe->getName()),
          'image' => 'cbimages/'.$record['picture_filename'],
          'title' => $recipe->getName(),
          'description' => $recipe->getDescription(),
        ]);
      }
    }
    parent::addCarousel($carousel);
    return parent::render(self::$template, $response);
  }

}
