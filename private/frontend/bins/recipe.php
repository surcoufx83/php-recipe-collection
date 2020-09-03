<?php

use Surcouf\Cookbook\Recipe\Cooking\BlankCookingStep;
use Surcouf\Cookbook\Recipe\Ingredients\BlankIngredient;
use Surcouf\Cookbook\Recipe\Pictures\BlankPicture;
use Surcouf\Cookbook\Recipe\BlankRecipe;
use Surcouf\Cookbook\Recipe\Social\Tags\BlankTag;
use Surcouf\Cookbook\Recipe\Ingredients\Units\BlankUnit;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\FilesystemHelper;
use Surcouf\Cookbook\Helper\UiHelper\CarouselHelper;
use Surcouf\Cookbook\Response\EOutputMode;

$Controller->get(array(
  'pattern' => '/(?<id>\d+)(/[^/]+)?',
  'fn' => 'ui_recipe'
));

$Controller->get(array(
  'pattern' => '/myrecipes',
  'fn' => 'ui_myrecipes'
));

$Controller->get(array(
  'pattern' => '/recipe/(un)?publish/(?<id>\d+)(/[^/]+)?',
  'fn' => 'ui_recipe_publish'
));

$Controller->get(array(
  'pattern' => '/recipe/new',
  'fn' => 'ui_new_recipe'
));

$Controller->post(array(
  'pattern' => '/recipe/new',
  'fn' => 'ui_post_new_recipe',
  'outputMode' => EOutputMode::JSON
));

function ui_myrecipes() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => lang('page_recipes_myrecipes'),
    'url' => $Controller->getLink('private:recipes'),
  );

  $recipes = [];
  $query = new QueryBuilder(EQueryType::qtSELECT, 'recipes', DB_ANY);
  $query->where('recipes', 'user_id', '=', $Controller->User()->getId());
  $result = $Controller->select($query);
  while ($record = $result->fetch_assoc()) {
    $recipe = $Controller->getRecipe($record);
    $Controller->loadRecipePictures($recipe);
    $Controller->loadRecipeRatings($recipe);
    $Controller->loadRecipeTags($recipe);
    $recipes[] = $recipe;
  }

  $OUT['Recipes'] = $recipes;
  $OUT['Page']['Current'] = 'private:recipes';
  $OUT['Page']['Heading1'] = lang('page_recipes_myrecipes');
  $OUT['Content'] = $twig->render('views/books/myrecipes.html.twig', $OUT);
} // ui_myrecipes()

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
    'url' => $Controller->getLink('recipe:show', $recipe->getId(), $recipe->getName()),
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

function ui_recipe_publish() {
  global $Controller, $OUT, $twig;

  $recipe = $Controller->getRecipe($Controller->Dispatcher()->getMatchInt('id'));

  if (is_null($recipe))
    $Controller->Dispatcher()->forward('/');

  if ($recipe->getUserId() != $Controller->User()->getId())
    $Controller->Dispatcher()->forward('/');

  $recipe->setPublic(!$recipe->isPublished());

  $Controller->Dispatcher()->forward($Controller->getLink('recipe:show', $recipe->getId(), $recipe->getName()));

} // ui_recipe_publish()

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

  $units = [];
  $query = new QueryBuilder(EQueryType::qtSELECT, 'units', DB_ANY);
  $query->orderBy('unit_name');
  $result = $Controller->select($query);
  if ($result) {
    while($record = $result->fetch_assoc()) {
      $units[] = $Controller->getUnit($record);
    }
  }

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
  $OUT['Units'] = $units;
  $OUT['Page']['Current'] = 'recipe:new';
  $OUT['Page']['Heading1'] = $Controller->l('newRecipe_header', $Controller->User()->getFirstname());
  $OUT['Page']['Scripts']['FormValidator'] = true;
  $OUT['Page']['Scripts']['Custom'][] = 'new-recipe-imguploader';
  $OUT['Content'] = $twig->render('views/recipes/new-recipe.html.twig', $OUT);
} // ui_new_recipe()

function ui_post_new_recipe() {
  global $Controller;

  $payload = $Controller->Dispatcher()->getPayload();

  $recipe = new BlankRecipe();
  $recipe->setDescription($payload['description'])
         ->setEaterCount(intval($payload['eater']))
         ->setName($payload['name'])
         ->setSourceDescription($payload['sourceText'])
         ->setSourceUrl($payload['sourceUrl']);

  for ($i=1; $i<count($payload['ingredient_description']); $i++) {
    if ($payload['ingredient_description'][$i] != '') {
      $unit = null;
      if ($payload['ingredient_unit'][$i] != '' && $payload['ingredient_unit'][$i] != '-1') {
        $unit = $Controller->getUnit(intval($payload['ingredient_unit'][$i]));
        if (is_null($unit) && !is_int($unit)) {
          $unit = new BlankUnit($payload['ingredient_unit'][$i]);
        }
      }
      $recipe->addNewIngredients(new BlankIngredient(
        floatval($payload['ingredient_quantity'][$i]),
        $unit,
        $payload['ingredient_description'][$i]
      ));
    }
  }

  for ($i=1; $i<count($payload['step_title']); $i++) {
    if ($payload['step_description'][$i] != '')
      $recipe->addNewStep(new BlankCookingStep(
        $recipe->getStepsCount()+1,
        $payload['step_title'][$i],
        $payload['step_description'][$i],
        $payload['step_duration_preparation'][$i],
        $payload['step_duration_rest'][$i],
        $payload['step_duration_cooking'][$i]
     ));
  }

  if (count($_FILES) > 0) {
    $picindex = 0;
    for ($i=1; $i<count($_FILES['pictures']['type']); $i++) {
      if ($_FILES['pictures']['error'][$i] != 0 || $_FILES['pictures']['size'][$i] == 0)
        continue;
      if (
        $_FILES['pictures']['type'][$i] != 'image/jpeg' &&
        $_FILES['pictures']['type'][$i] != 'image/jpg' &&
        $_FILES['pictures']['type'][$i] != 'image/png')
        continue;
      $recipe->addNewPicture(new BlankPicture(
        $picindex,
        $_FILES['pictures']['name'][$i],
        $_FILES['pictures']['tmp_name'][$i]
      ));
      $picindex++;
    }
  }

  if (array_key_exists('tags', $payload)) {
    for ($i=0; $i<count($payload['tags']); $i++) {
      $tag = $Controller->getTag($payload['tags'][$i]);
      if (is_null($tag))
        $tag = new BlankTag($payload['tags'][$i]);
      $recipe->addNewTag($tag);
    }
  }

  if ($Controller->startTransaction()) {

    $failed = false;

    // stmt. 1: insert recipe
    $id = $Controller->insertSimple(
      'recipes',
      ['user_id', 'recipe_name', 'recipe_description', 'recipe_eater',
       'recipe_source_desc', 'recipe_source_url'],
      [
        $recipe->getUserId(),
        $recipe->getName(),
        $recipe->getDescription(),
        $recipe->getEaterCount(),
        $recipe->getSourceDescription(),
        $recipe->getSourceUrl(),
      ]
    );
    if ($id == -1)
      $failed = true;
    else
      $recipe->setId($id);

    // stmt. 2: insert ingredients (and missing units)
    if (!$failed) {
      $insertedUnits = [];
      for ($i=0; $i<$recipe->getIngredientsCount(); $i++) {
        $obj = $recipe->getIngredients()[$i];
        $unit = $obj->getUnit();
        if (!is_null($unit) && get_class($unit) == 'Surcouf\Cookbook\Recipe\Ingredients\BlankUnit') {
          if (array_key_exists($unit->getName(), $insertedUnits))
            $unit->setId($insertedUnits[$unit->getName()]);
          else {
            $res = $Controller->insertSimple(
              'units',
              ['unit_name'],
              [$unit->getName()]
            );
            if ($res == false) {
              $failed = true;
              break;
            }
            $unit->setId($res);
            $insertedUnits[$unit->getName()] = $res;
          }
        }
        $res = $Controller->insertSimple(
          'recipe_ingredients',
          ['recipe_id', 'unit_id', 'ingredient_quantity', 'ingredient_description'],
          [$id, (!is_null($unit) ? $unit->getId() : null), $obj->getQuantity(), $obj->getDescription()]
        );
        if ($res == -1) {
          $failed = true;
          break;
        }
      }
    }

    // stmt. 3: insert steps
    if (!$failed) {
      for ($i=0; $i<$recipe->getStepsCount(); $i++) {
        $obj = $recipe->getSteps()[$i];
        $res = $Controller->insertSimple(
          'recipe_steps',
          ['recipe_id', 'step_no', 'step_title', 'step_data',
           'step_time_preparation', 'step_time_cooking', 'step_time_chill'],
          [
            $id,
            $obj->getStepNo(),
            $obj->getTitle(),
            $obj->getContent(),
            $obj->getPreparationTime(),
            $obj->getCookingTime(),
            $obj->getChillTime(),
          ]
        );
        if ($res == -1) {
          $failed = true;
          break;
        }
      }
    }

    // stmt. 4: insert tags
    if (!$failed) {
      $insertedTags = [];
      for ($i=0; $i<$recipe->getTagsCount(); $i++) {
        $obj = $recipe->getTags()[$i];
        if (get_class($obj) == 'Surcouf\Cookbook\Recipe\Social\Tags\BlankTag') {
          if (array_key_exists($obj->getName(), $insertedTags))
            $obj->setId($insertedTags[$obj->getName()]);
          else {
            $res = $Controller->insertSimple(
              'tags',
              ['tag_name'],
              [$obj->getName()]
            );
            if ($res == -1) {
              $failed = true;
              break;
            }
            $obj->setId($res);
            $insertedTags[$obj->getName()] = $res;
          }
        }
        $res = $Controller->insertSimple(
          'recipe_tags',
          ['recipe_id', 'tag_id', 'user_id'],
          [$id, $obj->getId(), $recipe->getUserId()]
        );
        if ($res == -1) {
          $failed = true;
          break;
        }
      }
    }

    // stmt. 5: insert pictures
    if (!$failed) {
      for ($i=0; $i<$recipe->getPictureCount(); $i++) {
        $obj = $recipe->getPictures()[$i];
        $filename =  $obj->getHash().$id.'.'.$obj->getExtension();
        if (!$obj->moveTo(FilesystemHelper::paths_combine(
            DIR_PUBLIC_IMAGES, 'cbimages', $filename))) {
          $failed = true;
          break;
        }
        $obj->setFilename($filename);
        $res = $Controller->insertSimple(
          'recipe_pictures',
          ['recipe_id', 'user_id', 'picture_sortindex', 'picture_name',
           'picture_description', 'picture_hash', 'picture_filename', 'picture_full_path'],
          [$id, $recipe->getUserId(), $obj->getIndex(), $obj->getName(),
           '', $obj->getHash(), $filename, $obj->getFullpath()]
        );
        if ($res == -1) {
          $failed = true;
          break;
        }
        $obj->setId($res);
      }
    }

    if ($failed) {
      $Controller->cancelTransaction();
      $response = $Controller->Config()->getResponseArray(10);
    } else {
      $Controller->finishTransaction();
      $response = $Controller->Config()->getResponseArray(1);
      $response['ForwardTo'] = $Controller->getLink('recipe:show', $id, $recipe->getName());
      $response['ForwardNew'] = $Controller->getLink('recipe:new');
    }

    return $response;

  }

  return $Controller->Config()->getResponseArray(10);

} // ui_post_new_recipe()
