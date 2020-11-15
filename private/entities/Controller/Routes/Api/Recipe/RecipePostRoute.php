<?php

namespace Surcouf\Cookbook\Controller\Routes\Api\Recipe;

use \DateTime;
use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\EActivityType;
use Surcouf\Cookbook\Recipe\BlankRecipe;
use Surcouf\Cookbook\Recipe\RecipeInterface;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\Pictures\BlankPicture;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

class RecipePostRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

    $recipe = $Controller->Dispatcher()->getObject();
    $payload = $Controller->Dispatcher()->getPayload();
    $action = $Controller->Dispatcher()->getFromMatches('action');
    $user = $Controller->User();
    if (array_key_exists('delete', $payload))
      return self::delete($response, $recipe, $user);
    if (array_key_exists('publish', $payload))
      return self::publish($response, $recipe, $user);
    if (array_key_exists('unpublish', $payload))
      return self::unpublish($response, $recipe, $user);
    if (array_key_exists('vote', $payload))
      return self::vote($response, $recipe, $user, $payload['vote']);
    if ($action == 'edit')
      return self::edit($response, $recipe, $user, $payload);
    if ($action == 'gallery') {
      if (array_key_exists('deleted', $payload))
        return self::pictureDeleted($response, $recipe, $user, $payload['deleted']);
      if (array_key_exists('moved', $payload))
        return self::pictureMoved($response, $recipe, $user, $payload['moved']);
      if (array_key_exists('pictureUpload', $_FILES))
        return self::pictureUploaded($response, $recipe, $user);
    }

    $response = $Controller->Config()->getResponseArray(71);
    parent::addToDictionary($response, ['response' => ['actionParam' => $action]]);

    return true;

  }

  static function delete(array &$response, RecipeInterface $recipe, ?UserInterface $user) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() != $user->getId()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    if ($recipe->delete()) {
      $response = $Controller->Config()->getResponseArray(1);
      $recipe = new BlankRecipe();
      return true;
    }
    $response = $Controller->Config()->getResponseArray(201);
    return false;
  }

  static function edit(array &$response, RecipeInterface $recipe, ?UserInterface $user, array $payload) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() != $user->getId()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    $recipe->loadComplete();
    return $recipe->update($response, $payload);
  }

  static function pictureDeleted(array &$response, RecipeInterface $recipe, ?UserInterface $user, array $params) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() != $user->getId()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    if (!array_key_exists('index', $params) || !array_key_exists('id', $params)) {
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }
    $index = intval($params['index']);
    $id = intval($params['id']);

    $picture = $Controller->OM()->Picture($id);
    if (is_null($picture)) {
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }

    $counts = $Controller->selectCountSimple('recipe_pictures', 'recipe_id', $recipe->getId());
    $counts -= 1;

    $failed = false;
    if (!$Controller->startTransaction())
      $failed = true;

    if (!$failed) {
      $query = new QueryBuilder(EQueryType::qtDELETE, 'recipe_pictures');
      $query->where('recipe_pictures', 'picture_id', '=', $picture->getId())
            ->limit(1);
      if (!$Controller->delete($query))
        $failed = true;
    }

    if (!$failed) {
      if ($picture->getIndex() < $counts) {
        for ($i = $picture->getIndex(); $i < $counts; $i++) {
          $query = new QueryBuilder(EQueryType::qtUPDATE, 'recipe_pictures');
          $query->update(['picture_sortindex' => $i])
                ->where('recipe_pictures', 'recipe_id', '=', $recipe->getId())
                ->andWhere('recipe_pictures', 'picture_sortindex', '=', ($i + 1))
                ->limit(1);
          if (!$Controller->update($query))
            $failed = true;
        }
      }
    }

    if (!$failed)
      $failed = !$Controller->finishTransaction();

    if ($failed) {
      $response = $Controller->Config()->getResponseArray(203);
      return false;
    }
    $response = $Controller->Config()->getResponseArray(1);
    return true;
  }

  static function pictureMoved(array &$response, RecipeInterface $recipe, ?UserInterface $user, array $params) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() != $user->getId()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    if (!array_key_exists('from', $params) || !array_key_exists('to', $params)) {
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }
    $from = intval($params['from']);
    $to = intval($params['to']);
    if ($from == $to) {
      $response = $Controller->Config()->getResponseArray(2);
      return true;
    }

    $counts = $Controller->selectCountSimple('recipe_pictures', 'recipe_id', $recipe->getId());
    if ($from >= $counts || $to >= $counts || $from < 0 || $to < 0) {
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }

    $failed = false;
    if (!$Controller->startTransaction())
      $failed = true;

    if (!$failed) {
      $query = new QueryBuilder(EQueryType::qtUPDATE, 'recipe_pictures');
      $query->update(['picture_sortindex' => $counts])
            ->where('recipe_pictures', 'recipe_id', '=', $recipe->getId())
            ->andWhere('recipe_pictures', 'picture_sortindex', '=', $from)
            ->limit(1);
      if (!$Controller->update($query))
        $failed = true;
    }

    if (!$failed) {
      if ($from < $to) {
        for ($i = $from; $i < $to; $i++) {
          $query = new QueryBuilder(EQueryType::qtUPDATE, 'recipe_pictures');
          $query->update(['picture_sortindex' => $i])
                ->where('recipe_pictures', 'recipe_id', '=', $recipe->getId())
                ->andWhere('recipe_pictures', 'picture_sortindex', '=', ($i + 1))
                ->limit(1);
          if (!$Controller->update($query))
            $failed = true;
        }
      } else {
        for ($i = $from; $i > $to; $i--) {
          $query = new QueryBuilder(EQueryType::qtUPDATE, 'recipe_pictures');
          $query->update(['picture_sortindex' => $i])
                ->where('recipe_pictures', 'recipe_id', '=', $recipe->getId())
                ->andWhere('recipe_pictures', 'picture_sortindex', '=', ($i - 1))
                ->limit(1);
          if (!$Controller->update($query))
            $failed = true;
        }
      }
    }

    if (!$failed) {
      $query = new QueryBuilder(EQueryType::qtUPDATE, 'recipe_pictures');
      $query->update(['picture_sortindex' => $to])
            ->where('recipe_pictures', 'recipe_id', '=', $recipe->getId())
            ->andWhere('recipe_pictures', 'picture_sortindex', '=', $counts)
            ->limit(1);
      if (!$Controller->update($query))
        $failed = true;
    }

    if (!$failed)
      $failed = !$Controller->finishTransaction();

    if ($failed) {
      $response = $Controller->Config()->getResponseArray(203);
      return false;
    }
    $response = $Controller->Config()->getResponseArray(1);
    return true;
  }

  static function pictureUploaded(array &$response, RecipeInterface $recipe, ?UserInterface $user) : bool {
    global $Controller;
    if (!$user) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }

    $filedata = $_FILES['pictureUpload'];
    $recipe->loadRecipePictures($Controller);
    $newindex = $recipe->getPictureCount();

    if ($filedata['error'] != 0 || $filedata['size'] == 0) {
      $response = $Controller->Config()->getResponseArray(301);
      return false;
    }

    if ($filedata['type'] != 'image/jpeg' &&
        $filedata['type'] != 'image/jpg' &&
        $filedata['type'] != 'image/png') {
      $response = $Controller->Config()->getResponseArray(302);
      return false;
    }

    $picture = new BlankPicture(
      $newindex,
      $filedata['name'],
      $filedata['tmp_name']
    );
    if (!$picture->moveTo($recipe->getId())) {
      $response = $Controller->Config()->getResponseArray(303);
      return false;
    }
    $res = $Controller->insertSimple(
      'recipe_pictures',
      ['recipe_id', 'user_id', 'picture_sortindex', 'picture_name',
       'picture_description', 'picture_hash', 'picture_filename', 'picture_full_path'],
      [$recipe->getId(), $user->getId(), $newindex, $picture->getName(),
       '', $picture->getHash(), $picture->getFilename(), $picture->getFullpath()]
    );
    if ($res == -1) {
      $response = $Controller->Config()->getResponseArray(202);
      return false;
    }

    $picture = $Controller->OM()->Picture(intval($res));

    $response = $Controller->Config()->getResponseArray(1);
    $response['picture'] = $picture;
    return true;
  }

  static function publish(array &$response, RecipeInterface $recipe, ?UserInterface $user) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() != $user->getId()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    if ($recipe->isPublished()) {
      $response = $Controller->Config()->getResponseArray(2);
      return true;
    }
    $recipe->setPublic(true);
    $response = $Controller->Config()->getResponseArray(1);
    parent::addToDictionary($response, ['page' => [ 'currentRecipe' => [ 'published' => $recipe->getPublishedDate()->format(DateTime::ISO8601) ]]]);
    return true;
  }

  static function unpublish(array &$response, RecipeInterface $recipe, ?UserInterface $user) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() != $user->getId()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    if (!$recipe->isPublished()) {
      $response = $Controller->Config()->getResponseArray(2);
      return true;
    }
    $recipe->setPublic(false);
    $response = $Controller->Config()->getResponseArray(1);
    parent::addToDictionary($response, ['page' => [ 'currentRecipe' => [ 'published' => false ]]]);
    return true;
  }

  static function vote(array &$response, RecipeInterface $recipe, ?UserInterface $user, array $voting) : bool {
    global $Controller;
    if (!$user || $recipe->getUserId() == $user->getId() || !$recipe->isPublished()) {
      $response = $Controller->Config()->getResponseArray(92);
      return false;
    }
    $cooked = intval($voting['cooked']);
    $rated = intval($voting['rating']);
    $voted = intval($voting['voting']);
    if (($cooked < -1 && $cooked > 1) ||
      ($rated < -1 && $rated > 3) ||
      ($voted < 1 && $voted > 5) ||
      ($cooked == -1 && $rated == -1 && $voted == -1)) {
      $response = $Controller->Config()->getResponseArray(80);
      return false;
    }
    $maxage = (new DateTime())->sub(new \DateInterval($Controller->Config()->Page('Timespans', 'BetweenVotes')));
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_ratings', DB_ANY);
    $query->where('recipe_ratings', 'entry_datetime', '>=', Formatter::date_format($maxage, DTF_SQL))
          ->andWhere('recipe_ratings', 'recipe_id', '=', $recipe->getId())
          ->andWhere('recipe_ratings', 'user_id', '=', $Controller->User()->getId())
          ->andWhere('recipe_ratings', 'entry_viewed', 'IS NULL');
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

    $result = $Controller->insertSimple(
      'recipe_ratings',
      ['user_id', 'recipe_id', 'entry_cooked', 'entry_vote', 'entry_rate'],
      [$Controller->User()->getId(),
       $recipe->getId(),
       $cooked > -1 ? $cooked : null,
       $voted > -1 ? $voted : null,
       $rated > -1 ? $rated : null]
    );

    if ($result != -1) {
      $id = $Controller->getInsertId();
      $Controller->addActivity(
        EActivityType::RatingAdded, [
          'rating_id' => $id,
        ], $recipe, null, $id);
      $response = $Controller->Config()->getResponseArray(1);
      return true;
    }
    $response = $Controller->Config()->getResponseArray(202);
    return false;
  }

}
