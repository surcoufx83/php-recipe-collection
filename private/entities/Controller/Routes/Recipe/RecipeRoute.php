<?php

namespace Surcouf\Cookbook\Controller\Routes\Recipe;

use \DateTime;
use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\Helper\UiHelper\CarouselHelper;

if (!defined('CORE2'))
  exit;

class RecipeRoute extends Route implements RouteInterface {

  private static $template = 'recipes/recipe';

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;

    $recipe = $Controller->Dispatcher()->getObject();
    if (!$recipe->isPublished() && $recipe->getUserId() != $Controller->User()->getId())
      $Controller->Dispatcher()->forwardTo($Controller->getLink('private:home'));
    $recipe->loadComplete();
    if ($recipe->getUserId() != $Controller->User()->getId()) {
      $maxage = (new DateTime())->sub($Controller->Config()->RecipeVisitedClearance());
      $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings', DB_ANY);
      $query->where('recipe_ratings', 'entry_datetime', '>=', Formatter::date_format($maxage, DTF_SQL))
            ->andWhere('recipe_ratings', 'recipe_id', '=', $recipe->getId())
            ->andWhere('recipe_ratings', 'user_id', '=', $Controller->User()->getId())
            ->andWhere('recipe_ratings', 'entry_viewed', '=', '1');
      $result = $Controller->select($query);
      if ($result && $result->num_rows > 0) {
        $keys = [];
        while ($record = $result->fetch_assoc()) {
          $keys[] = intval($record['entry_id']);
        }
        $query = new QueryBuilder(EQueryType::qtDELETE, 'recipe_ratings');
        $query->where('recipe_ratings', 'entry_id', 'IN', $keys)
              ->limit(count($keys));
        $Controller->delete($query);
      }
      $Controller->insertSimple(
        'recipe_ratings',
        ['user_id', 'recipe_id', 'entry_viewed'],
        [$Controller->User()->getId(), $recipe->getId(), 1]
      );
    }
    $maxage = (new DateTime())->sub($Controller->Config()->RecipeRatingClearance());
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings', DB_ANY);
    $query->where('recipe_ratings', 'entry_datetime', '>=', Formatter::date_format($maxage, DTF_SQL))
          ->andWhere('recipe_ratings', 'recipe_id', '=', $recipe->getId())
          ->andWhere('recipe_ratings', 'user_id', '=', $Controller->User()->getId())
          ->andWhere('recipe_ratings', 'entry_comment', 'IS NULL')
          ->andWhere('recipe_ratings', 'entry_viewed', 'IS NULL')
          ->orderBy2('recipe_ratings', 'entry_datetime', 'DESC')
          ->limit(1);
    $result = $Controller->select($query);
    $myvote = false;
    if ($result && $result->num_rows > 0)
      $myvote = $Controller->OM()->Rating($result->fetch_assoc());

    parent::addBreadcrumb($Controller->getLink('private:home'), $Controller->l('breadcrumb_recipes'));
    if (!is_null($recipe->getUserId()))
      parent::addBreadcrumb($Controller->getLink('user:recipes', $recipe->getUserId(), $recipe->getUser()->getFirstname()), $recipe->getUser()->getName());
    parent::addBreadcrumb($Controller->getLink('recipe:show', $recipe->getId(), $recipe->getName()), $recipe->getName());

    $pics = $recipe->getPictures();
    if (count($pics) > 0) {
      $carousel = CarouselHelper::createNew('recipe-'.$recipe->getId().'-pictures', true, true);
      for ($i = 0; $i < count($pics); $i++) {
        CarouselHelper::addItem($carousel, [
          'href' => $Controller->getLink('recipe:picture:link', $pics[$i]->getFilename()),
          'image' => 'cbimages/'.$pics[$i]->getFilename(),
          'title' => '',
          'description' => $pics[$i]->getDescription(),
        ]);
      }
      parent::addToPage('Gallery', CarouselHelper::render($carousel));
    }

    parent::addToDictionary('MyVote', $myvote);
    parent::addToDictionary('Recipe', $recipe);

    parent::addRatingScript();
    parent::addScript('recipe-show');
    parent::setPage('private:home');
    parent::setTitle((!is_null($recipe->getUserId()) ? $Controller->l('greetings_recipeFrom', $recipe->getName(), $recipe->getUser()->getFirstname()) : $recipe->getName()));
    return parent::render(self::$template, $response);
  }

}
