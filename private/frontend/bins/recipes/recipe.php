<?php

use Surcouf\PhpArchive\Helper\UiHelper\CarouselHelper;

$Controller->get(array(
  'pattern' => '/r/(?<id>\d+)',
  'fn' => 'ui_recipe'
));

function ui_recipe() {
  global $Controller, $OUT, $twig;

  $recipe = $Controller->getRecipe($Controller->Dispatcher()->getMatchInt('id'));
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
  $OUT['Page']['Current'] = 'private:home';
  $OUT['Page']['Heading1'] = (!is_null($recipe->getUserId()) ? lang('greetings_recipeFrom', [$recipe->getName(), $recipe->getUser()->getFirstname()]) : $recipe->getName());
  $OUT['Content'] = $twig->render('views/recipes/recipe.html.twig', $OUT);
} // ui_home()
