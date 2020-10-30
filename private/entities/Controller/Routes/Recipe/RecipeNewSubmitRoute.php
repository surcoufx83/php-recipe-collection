<?php

namespace Surcouf\Cookbook\Controller\Routes\Recipe;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\FilesystemHelper;
use Surcouf\Cookbook\Recipe\BlankRecipe;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\Cooking\BlankCookingStep;
use Surcouf\Cookbook\Recipe\Ingredients\BlankIngredient;
use Surcouf\Cookbook\Recipe\Ingredients\Units\BlankUnit;
use Surcouf\Cookbook\Recipe\Pictures\BlankPicture;
use Surcouf\Cookbook\Recipe\Social\Tags\BlankTag;

if (!defined('CORE2'))
  exit;

class RecipeNewSubmitRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
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
          $unit = $Controller->OM()->Unit(intval($payload['ingredient_unit'][$i]));
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
          $payload['step_duration_cooking'][$i],
          $payload['step_duration_rest'][$i]
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
        $tag = $Controller->OM()->Tag($payload['tags'][$i]);
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
          if (!is_null($unit) && !$unit->hasId()) {
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
          if (!$obj->moveTo(FilesystemHelper::paths_combine(
              DIR_PUBLIC_IMAGES, 'cbimages'), $id)) {
            $failed = true;
            break;
          }
          $res = $Controller->insertSimple(
            'recipe_pictures',
            ['recipe_id', 'user_id', 'picture_sortindex', 'picture_name',
             'picture_description', 'picture_hash', 'picture_filename', 'picture_full_path'],
            [$id, $recipe->getUserId(), $obj->getIndex(), $obj->getName(),
             '', $obj->getHash(), $obj->getFilename(), $obj->getFullpath()]
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
        return false;
      } else {
        $Controller->finishTransaction();
        $response = $Controller->Config()->getResponseArray(1);
        $response['ForwardTo'] = $Controller->getLink('recipe:show', $id, $recipe->getName());
        $response['ForwardNew'] = $Controller->getLink('recipe:new');
        return true;
      }
    }
    $response = $Controller->Config()->getResponseArray(10);
    return false;
  }

}
