<?php

use Surcouf\PhpArchive\Database\EAggregationType;
use Surcouf\PhpArchive\Database\EQueryType;
use Surcouf\PhpArchive\Database\QueryBuilder;
use Surcouf\PhpArchive\Helper\UiHelper\CarouselHelper;

$Controller->get(array(
  'pattern' => '/(?<id>\d+)(/[^/]+)?',
  'fn' => 'ui_recipe'
));

$Controller->get(array(
  'pattern' => '/recipe/new',
  'fn' => 'ui_new_recipe'
));

function ui_recipe() {
  global $Controller, $OUT, $twig;

  $recipe = $Controller->getRecipe($Controller->Dispatcher()->getMatchInt('id'));

  if (!$recipe->isPublished() && $recipe->getUserId() != $Controller->User()->getId())
    $Controller->Dispatcher()->forward('/');

  $recipe->loadComplete();

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => lang('breadcrumb_recipes'),
    'url' => $Controller->getLink('private:home'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $recipe->getName(),
    'url' => $Controller->getLink('recipe:show:'.$recipe->getId()),
  );

  $pics = $recipe->getPictures();
  if (count($pics) > 0) {
    $carousel = CarouselHelper::createNew('recipe-'.$recipe->getId().'-pictures', true);
    for ($i = 0; $i < count($pics); $i++) {
      CarouselHelper::addItem($carousel, [
        'href' => null,
        'image' => 'cbimages/'.$pics[$i]->getFilename(),
        'title' => '',
        'description' => $pics[$i]->getDescription(),
      ]);
    }
    $OUT['Page']['Gallery'] = CarouselHelper::render($carousel);
  }

  $OUT['Recipe'] = $recipe;
  $OUT['Page']['Heading1'] = (!is_null($recipe->getUserId()) ? lang('greetings_recipeFrom', [$recipe->getName(), $recipe->getUser()->getFirstname()]) : $recipe->getName());
  $OUT['Content'] = $twig->render('views/recipes/recipe.html.twig', $OUT);
} // ui_recipe()

function ui_new_recipe() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_recipes'),
    'url' => $Controller->getLink('private:home'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => $Controller->l('breadcrumb_newRecipe'),
    'url' => $Controller->getLink('recipe:new'),
  );

  $tags = [];
  $query = new QueryBuilder(EQueryType::qtSELECT, 'tags', DB_ANY);
  $query->orderBy('tag_name');
  $result = $Controller->select($query);
  if ($result) {
    while($record = $result->fetch_assoc()) {
      $tags[] = $Controller->getTag($record);
    }
  }

  $OUT['Tags'] = $tags;
  $OUT['Page']['Heading1'] = $Controller->l('newRecipe_header', $Controller->User()->getFirstname());
  $OUT['Page']['Scripts']['FormValidator'] = true;
  $OUT['Content'] = $twig->render('views/recipes/new-recipe.html.twig', $OUT);
} // ui_new_recipe()
