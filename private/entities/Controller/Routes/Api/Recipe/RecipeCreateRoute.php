<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\Recipe;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\Formatter;
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

class RecipeCreateRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

    $payload = $Controller->Dispatcher()->getPayload();

    $recipe = new BlankRecipe();
    $recipe->setDescription($payload['recipe-description'])
           ->setEaterCount(intval($payload['recipe-eater']))
           ->setName($payload['recipe-name'])
           ->setSourceDescription($payload['recipe-source'])
           ->setSourceUrl($payload['recipe-sourceurl']);

    self::addIngredients($recipe, $payload);
    self::addSteps($recipe, $payload);
    self::addFiles($recipe, $payload);

    $response = $Controller->Config()->getResponseArray(1);
    $failed = false;
    if ($Controller->startTransaction()) {
      if (
        self::saveRecipe($response, $recipe, $failed) &&
        self::saveIngredients($response, $recipe, $failed) &&
        self::saveSteps($response, $recipe, $failed) &&
        self::savePictures($response, $recipe, $failed)
        )
        $Controller->finishTransaction();
    } else {
      $response = $Controller->Config()->getResponseArray(201);
      return false;
    }

    if ($failed) {
      $Controller->cancelTransaction();
      $response = $Controller->Config()->getResponseArray(202);
      return false;
    }

    parent::addToDictionary($response, ['recipeId' => $recipe->getId()]);

    return true;
  }

  static function addIngredients(BlankRecipe &$recipe, array &$payload) : void {
    global $Controller;
    for ($i=0; $i<count($payload['recipe-ingredient-description']); $i++) {
      if ($payload['recipe-ingredient-description'][$i] != '') {
        $unit = null;
        if ($payload['recipe-ingredient-unit'][$i] != '') {
          $unit = $Controller->OM()->Unit($payload['recipe-ingredient-unit'][$i]);
          if (is_null($unit) && !is_int($unit)) {
            $unit = new BlankUnit($payload['recipe-ingredient-unit'][$i]);
          }
        }
        $recipe->addNewIngredients(new BlankIngredient(
          floatval($payload['recipe-ingredient-quantity'][$i]),
          $unit,
          $payload['recipe-ingredient-description'][$i]
        ));
      }
    }
  }

  static function addSteps(BlankRecipe &$recipe, array &$payload) : void {
    global $Controller;
    for ($i=0; $i<count($payload['recipe-step-title']); $i++) {
      if ($payload['recipe-step-description'][$i] != '') {
        $recipe->addNewStep(new BlankCookingStep(
          $recipe->getStepsCount()+1,
          $payload['recipe-step-title'][$i],
          $payload['recipe-step-description'][$i],
          $payload['recipe-step-time-prep'][$i],
          $payload['recipe-step-time-rest'][$i],
          $payload['recipe-step-time-cook'][$i]
        ));
      }
    }
  }

  static function addFiles(BlankRecipe &$recipe, array &$payload) : void {
    global $Controller;
    if (count($_FILES) > 0) {
      $picindex = 0;
      foreach ($_FILES as $key => $object) {
        if ($object['error'] != 0 || $object['size'] == 0)
          continue;
        if ($object['type'] != 'image/jpeg' &&
            $object['type'] != 'image/jpg' &&
            $object['type'] != 'image/png')
          continue;
        $recipe->addNewPicture(new BlankPicture(
          $picindex,
          $object['name'],
          $object['tmp_name']
        ));
        $picindex++;
      }
    }
  }

  static function saveRecipe(array &$response, BlankRecipe &$recipe, bool &$failed) : bool {
    global $Controller;
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
    if ($id == -1) {
      $failed = true;
      return false;
    }
    $recipe->setId($id);
    return true;
  }

  static function saveIngredients(array &$response, BlankRecipe &$recipe, bool &$failed) : bool {
    global $Controller;

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
          if ($res == -1) {
            $failed = true;
            return false;
          }
          $unit->setId($res);
          $insertedUnits[$unit->getName()] = $res;
        }
      }
      $res = $Controller->insertSimple(
        'recipe_ingredients',
        ['recipe_id', 'unit_id', 'ingredient_quantity', 'ingredient_description'],
        [$recipe->getId(), (!is_null($unit) ? $unit->getId() : null), $obj->getQuantity(), $obj->getDescription()]
      );
      if ($res == -1) {
        $failed = true;
        return false;
      }
    }
    return true;
  }

  static function saveSteps(array &$response, BlankRecipe &$recipe, bool &$failed) : bool {
    global $Controller;
    for ($i=0; $i<$recipe->getStepsCount(); $i++) {
      $obj = $recipe->getSteps()[$i];
      $res = $Controller->insertSimple(
        'recipe_steps',
        ['recipe_id', 'step_no', 'step_title', 'step_data',
         'step_time_preparation', 'step_time_cooking', 'step_time_chill'],
        [
          $recipe->getId(),
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
        return false;
      }
    }
    return true;
  }

  static function savePictures(array &$response, BlankRecipe &$recipe, bool &$failed) : bool {
    global $Controller;
    for ($i=0; $i<$recipe->getPictureCount(); $i++) {
      $obj = $recipe->getPictures()[$i];
      if (!$obj->moveTo(FilesystemHelper::paths_combine(
          DIR_PUBLIC_IMAGES, 'cbimages'), $recipe->getId())) {
            $failed = true;
            return false;
      }
      $res = $Controller->insertSimple(
        'recipe_pictures',
        ['recipe_id', 'user_id', 'picture_sortindex', 'picture_name',
         'picture_description', 'picture_hash', 'picture_filename', 'picture_full_path'],
        [$recipe->getId(), $recipe->getUserId(), $obj->getIndex(), $obj->getName(),
         '', $obj->getHash(), $obj->getFilename(), $obj->getFullpath()]
      );
      if ($res == -1) {
        $failed = true;
        return false;
      }
      $obj->setId($res);
    }
    return true;
  }

}
