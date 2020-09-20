<?php

namespace Surcouf\Cookbook\Controller\Routes;

use Surcouf\Cookbook\ControllerInterface;
use Surcouf\Cookbook\EActivityType;
use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\Helper\UiHelper\GalleryHelper;
use Surcouf\Cookbook\Helper\UiHelper\GalleryItem;
use Surcouf\Cookbook\Helper\UiHelper\GalleryItemInterface;
use Surcouf\Cookbook\Recipe\Recipe;
use Surcouf\Cookbook\Recipe\RecipeInterface;

if (!defined('CORE2'))
  exit;

final class UserHomeRoute extends Route implements RouteInterface {

  private static $template = 'home';

  static function createOutput(array &$response) : bool {
    global $Controller, $OUT;

    parent::addBreadcrumb($Controller->getLink('private:home'), $Controller->l('breadcrumb_home'));
    parent::setPage('private:home');
    parent::setTitle($Controller->l('greetings_hello', $Controller->User()->getFirstname()));

    $query = new QueryBuilder(EQueryType::qtSELECT, 'activities', DB_ANY);
    $query
      ->select('users', ['user_name', 'oauth_user_name'])
      ->select('recipes', ['recipe_name', 'recipe_description', 'recipe_eater'])
      ->select('recipe_pictures', ['picture_description', 'picture_filename'])
      ->select('recipe_ratings', ['entry_comment', 'entry_cooked', 'entry_vote', 'entry_rate'])
      ->select('tags', ['tag_name'])
      ->joinLeft('users', ['users', 'user_id', '=', 'activities', 'user_id'])
      ->joinLeft('recipes',
          ['recipes', 'recipe_id', '=', 'activities', 'recipe_id'],
          ['AND', 'recipes', 'recipe_public', '=', 1])
      ->joinLeft('recipe_pictures', ['recipe_pictures', 'picture_id', '=', 'activities', 'picture_id'])
      ->joinLeft('recipe_ratings', ['recipe_ratings', 'entry_id', '=', 'activities', 'rating_id'])
      ->joinLeft('recipe_tags', ['recipe_tags', 'entry_id', '=', 'activities', 'tag_id'])
      ->joinLeft('tags', ['tags', 'tag_id', '=', 'recipe_tags', 'tag_id'])
      ->orderBy2('activities', 'entry_timestamp', 'DESC')
      ->limit(20);
    $result = $Controller->select($query);
    $gallery = new GalleryHelper();
    if ($result) {
      while($record = $result->fetch_array()) {
        $item = new GalleryItem();
        $time = new \DateTime($record['entry_timestamp']);
        $data = json_decode($record['entry_data'], true);
        $userid = intval($data['user_id']);
        $username = $data['user_name'];
        if (!is_null($record['user_id'])) {
          $userid = intval($record['user_id']);
          if (!is_null($record['oauth_user_name']))
            $username = $record['oauth_user_name'];
          elseif (!is_null($record['user_name']))
            $username = $record['user_name'];
        }
        $recipeid = intval($data['recipe_id']);
        $recipename = $data['recipe_name'];
        if (!is_null($record['recipe_id'])) {
          $recipeid = intval($record['recipe_id']);
          if (!is_null($record['recipe_name']))
            $recipename = $record['recipe_name'];
        }

        $recipe = $Controller->OM()->Recipe($recipeid);
        $recipe->loadRecipePictures($Controller);

        $type = intval($record['entry_type']);
        switch($type) {

          case EActivityType::RecipePublished:
            self::createRecipeItem($item, $Controller, $recipe, $record, $data, $time, $userid, $username, $recipeid, $recipename);
            break;

          case EActivityType::PictureAdded:
            self::createImageItem($item, $Controller, $recipe, $record, $data, $time, $userid, $username, $recipeid, $recipename);
            break;

          case EActivityType::RatingAdded:
            self::createRatingItem($item, $Controller, $recipe, $record, $data, $time, $userid, $username, $recipeid, $recipename);
            break;

          default:
            continue 2;

        }
        $item
          ->setFooterAction(
            $Controller->l('page_home_gallery_items_actions_goto_recipe'),
            $Controller->getLink('recipe:show', $recipe->getId(), $recipe->getName())
          )
          ->setFooterNote(Formatter::date_format($time));
        if (is_null($item->getImageUrl()) && $recipe->hasPictures()) {
          $pic = $recipe->getPictures()[0];
          $item->setImage($Controller->getLink('recipe:picture:link', $pic->getFilename()));
        }
        $gallery->addItem($item);
      }
    }
    parent::addToPage('Gallery', $gallery->render());
    return parent::render(self::$template, $response);
  }

  private static function createImageItem(
    GalleryItemInterface &$item,
    ControllerInterface &$Controller,
    RecipeInterface &$recipe,
    array &$record, array &$data,
    \DateTime $time,
    ?int $userid, ?string $username, ?int $recipeid, ?string $recipename) {
    $item
      ->setImage($Controller->getLink('recipe:picture:link', $record['picture_filename']))
      ->setBody(
        $Controller->l('page_home_gallery_items_imageAdded_title'),
        $Controller->l('page_home_gallery_items_imageAdded_description', $username, $recipename)
        );
    if (array_key_exists('picture_description', $data))
      $item->setQuote($data['picture_description']);
  }

  private static function createRecipeItem(
    GalleryItemInterface &$item,
    ControllerInterface &$Controller,
    RecipeInterface &$recipe,
    array &$record, array &$data,
    \DateTime $time,
    ?int $userid, ?string $username, ?int $recipeid, ?string $recipename) {
    $item
      ->setBody(
        $Controller->l('page_home_gallery_items_recipeAdded_title'),
        $Controller->l('page_home_gallery_items_recipeAdded_description', $username, $recipename)
        );
  }

  private static function createRatingItem(
    GalleryItemInterface &$item,
    ControllerInterface &$Controller,
    RecipeInterface &$recipe,
    array &$record, array &$data,
    \DateTime $time,
    ?int $userid, ?string $username, ?int $recipeid, ?string $recipename) {

    $body = $Controller->l('page_home_gallery_items_ratingAdded_descriptionIntro', $username, $recipename);
    if ($record['entry_cooked'] == 1 && (!is_null($record['entry_vote']) || !is_null($record['entry_rate'])))
      $body .= ' '.$Controller->l('page_home_gallery_items_ratingAdded_descriptionCookedAndVoted');
    elseif ($record['entry_cooked'] == 1)
      $body .= ' '.$Controller->l('page_home_gallery_items_ratingAdded_descriptionCooked');
    else
      $body .= ' '.$Controller->l('page_home_gallery_items_ratingAdded_descriptionVoted');

    $item->setBody($Controller->l('page_home_gallery_items_ratingAdded_title'), $body);

    $toughness = '';
    if ($record['entry_rate'] == 1)
      $toughness = $Controller->l('common_rating_easyTo');
    elseif ($record['entry_rate'] == 2)
      $toughness = $Controller->l('common_rating_mediumTo');
    elseif ($record['entry_rate'] == 3)
      $toughness = $Controller->l('common_rating_hardTo');

    $item->setQuoteIcon($Controller->Config()->Icons()->Meal('mr-1'));
    $items = [];
    if (!is_null($record['entry_rate']))
      $items[] = $Controller->l('page_home_gallery_items_ratingAdded_descriptionRated', $toughness);
    if (!is_null($record['entry_vote']))
      $items[] = $record['entry_vote'].$Controller->Config()->Icons()->Star('text-small ml-1');
    $item->setQuote(join(', ', $items));

  }

}
