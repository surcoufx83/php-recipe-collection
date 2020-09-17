<?php

use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\UiHelper\CarouselHelper;
use Surcouf\Cookbook\Recipe\Recipe;

$Controller->get(array(
  'pattern' => '/',
  'ignoreMaintenance' => true,
  'fn' => 'ui_home'
));

$Controller->get(array(
  'pattern' => '/',
  'ignoreMaintenance' => true,
  'requiresAuthentication' => false,
  'fn' => 'ui_anonymous_home'
));

$Controller->get(array(
  'pattern' => '/maintenance',
  'ignoreMaintenance' => true,
  'requiresAuthentication' => false,
  'fn' => 'ui_maintenance'
));

function ui_home() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => lang('breadcrumb_home'),
    'url' => $Controller->getLink('private:home'),
  );

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

  $OUT['Page']['Carousel'] = CarouselHelper::render($carousel);

  $OUT['Page']['Current'] = 'private:home';
  $OUT['Page']['Heading1'] = lang('greetings_hello', $Controller->User()->getFirstname());
  $OUT['Content'] = $twig->render('views/home.html.twig', $OUT);
} // ui_home()

function ui_anonymous_home() {
  global $OUT, $twig;
  //$OUT['Content'] = $twig->render('views/user/home.html.twig', $OUT);
} // ui_anonymous_home()

function ui_maintenance() {
  global $OUT, $Router;
  if (!MAINTENANCE)
    $Router->forward('/');
  $OUT['Page']['Heading1'] = 'Maintenance mode - Restricted access';
} // ui_maintenance()
