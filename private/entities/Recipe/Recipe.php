<?php

namespace Surcouf\Cookbook\Recipe;

use \DateTime;
use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\Recipe\Cooking\CookingStepInterface;
use Surcouf\Cookbook\Recipe\Ingredients\IngredientInterface;
use Surcouf\Cookbook\Recipe\Pictures\PictureInterface;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Recipe\Social\Tags\TagInterface;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

class Recipe implements RecipeInterface, DbObjectInterface {

  protected $id, $userid, $ispublic, $name, $description, $eater, $sourcedesc, $sourceurl, $created, $published;
  protected $timecooking = 0, $timepreparation = 0, $timerest = 0;
  protected $countcooked = 0, $countrated = 0, $countviewed = 0, $countvoted = 0;
  protected $sumvoted = 0, $sumrated = 0;
  protected $ingredients = array();
  protected $pictures = array();
  protected $ratings = array();
  protected $steps = array();
  protected $tags = array();
  protected $tagvotes = array();
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['recipe_id']);
    $this->userid = (!is_null($dr['user_id']) ? intval($dr['user_id']) : null);
    $this->ispublic = ConverterHelper::to_bool($dr['recipe_public']);
    $this->name = $dr['recipe_name'];
    $this->description = $dr['recipe_description'];
    $this->eater = intval($dr['recipe_eater']);
    $this->sourcedesc = $dr['recipe_source_desc'];
    $this->sourceurl = $dr['recipe_source_url'];
    $this->created = new DateTime($dr['recipe_created']);
    $this->published = (!is_null($dr['recipe_published']) ? new DateTime($dr['recipe_published']) : null);
  }

  public function addIngredients(IngredientInterface &$ingredient) : void {
    $this->ingredients[$ingredient->getId()] = $ingredient;
  }

  public function addPicture(PictureInterface &$picture) : void {
    $this->pictures[$picture->getIndex()] = $picture;
  }

  public function addRating(RatingInterface &$rating) : void {
    $this->ratings[] = $rating;
    if ($rating->hasCooked())
      $this->countcooked++;
    if ($rating->hasRated()) {
      $this->countrated++;
      $this->sumrated += $rating->getRating();
    }
    if ($rating->hasViewed())
      $this->countviewed++;
    if ($rating->hasVoted()) {
      $this->countvoted++;
      $this->sumvoted += $rating->getVoting();
    }
  }

  public function addStep(CookingStepInterface &$step) : void {
    if ($step->getPreparationTime() > 0)
      $this->timepreparation += $step->getPreparationTime();
    if ($step->getCookingTime() > 0)
      $this->timecooking += $step->getCookingTime();
    if ($step->getChillTime() > 0)
      $this->timerest += $step->getChillTime();
    $this->steps[$step->getIndex()] = $step;
  }

  public function addTag(TagInterface &$tag, int $votes) : void {
    $this->tags[$tag->getId()] = $tag;
    $this->tagvotes[$tag->getId()] = $votes;
  }

  public function getCookedCount() : int {
    return $this->countcooked;
  }

  public function getCookedCountStr() : string {
    return Formatter::int_format($this->countcooked);
  }

  public function getCookingTime() : ?int {
    return $this->timecooking > 0 ?? null;
  }

  public function getCookingTimeStr() : ?string {
    return $this->timecooking > 0 ? Formatter::min_format($this->timecooking) : null;
  }

  public function getCreationDate() : DateTime {
    return $this->created;
  }

  public function getCreationDateStr() : string {
    return Formatter::date_format($this->created);
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getDescription() : string {
    return $this->description;
  }

  public function getEaterCount() : int {
    return $this->eater;
  }

  public function getEaterCountStr() : string {
    return Formatter::int_format($this->eater);
  }

  public function getId() : int {
    return $this->id;
  }

  public function getIngredients() : array {
    return $this->ingredients;
  }

  public function getIngredientsCount() : int {
    return count($this->ingredients);
  }

  public function getName() : string {
    return $this->name;
  }

  public function getOverallTime() : ?int {
    $sum = $this->timepreparation + $this->timecooking + $this->timerest;
    return $sum > 0 ? $sum : null;
  }

  public function getPictures() : array {
    return $this->pictures;
  }

  public function getPictureCount() : int {
    return count($this->pictures);
  }

  public function getPreparationTime() : ?int {
    return $this->timepreparation > 0 ?? null;
  }

  public function getPreparationTimeStr() : ?string {
    return $this->timepreparation > 0 ? Formatter::min_format($this->timepreparation) : null;
  }

  public function getPublishedDate() : ?DateTime {
    return $this->published;
  }

  public function getPublishedDateStr() : string {
    return Formatter::date_format($this->published);
  }

  public function getRatedCount() : int {
    return $this->countrated;
  }

  public function getRatedCountStr() : string {
    return Formatter::int_format($this->countrated);
  }

  public function getRating() : ?float {
    if ($this->countrated == 0)
      return null;
    return round(floatval($this->sumrated) / floatval($this->countrated), 1);
  }

  public function getRatingStr() : string {
    global $Controller;
    if ($this->countrated == 0)
      return '';
    $val = round($this->getRating(), 0);
    switch($val) {
      case 1:
        return $Controller->l('common_rating_easy');
      case 2:
        return $Controller->l('common_rating_medium');
      case 3:
        return $Controller->l('common_rating_hard');
    }
    return '';
  }

  public function getRatings() : array {
    return $this->ratings;
  }

  public function getRestTime() : ?int {
    return $this->timerest > 0 ? $this->timerest : null;
  }

  public function getRestTimeStr() : ?string {
    return $this->timerest > 0 ? Formatter::min_format($this->timerest) : null;
  }

  public function getSourceDescription() : string {
    return $this->sourcedesc;
  }

  public function getSourceUrl() : string {
    return $this->sourceurl;
  }

  public function getSteps() : array {
    return $this->steps;
  }

  public function getStepsCount() : int {
    return count($this->steps);
  }

  public function getTags() : array {
    return $this->tags;
  }

  public function getTagsCount() : int {
    return count($this->tags);
  }

  public function getTagVotes() : array {
    return $this->tagvotes;
  }

  public function getUser() : ?UserInterface {
    global $Controller;
    return $Controller->getUser($this->userid);
  }

  public function getUserId() : ?int {
    return $this->userid;
  }

  public function getViewedCount() : int {
    return $this->countviewed;
  }

  public function getViewedCountStr() : string {
    return Formatter::int_format($this->countviewed);
  }

  public function getVotedCount() : int {
    return $this->countvoted;
  }

  public function getVotedCountStr() : string {
    return Formatter::int_format($this->countvoted);
  }

  public function getVoting() : ?float {
    if ($this->countvoted == 0)
      return null;
    return round(floatval($this->sumvoted) / floatval($this->countvoted), 1);
  }

  public function getVotingStr() : string {
    if ($this->countvoted == 0)
      return '';
    return Formatter::float_format($this->getVoting(), 1);
  }

  public function isPublished() : bool {
    return $this->ispublic;
  }

  public function hasPictures() : bool {
    return count($this->pictures) > 0;
  }

  public function loadComplete() : void {
    global $Controller;
    $Controller->loadRecipeIngredients($this);
    $Controller->loadRecipePictures($this);
    $Controller->loadRecipeRatings($this);
    $Controller->loadRecipeSteps($this);
    $Controller->loadRecipeTags($this);
  }

  public function setDescription(string $newDescription) : RecipeInterface {
    global $Controller;
    $this->description = $newDescription;
    $this->changes['recipe_description'] = $newDescription;
    $Controller->updateDbObject($this);
    return $this;
  }

  public function setEaterCount(int $newCount) : RecipeInterface {
    global $Controller;
    $this->eater = $newCount;
    $this->changes['recipe_eater'] = $newCount;
    $Controller->updateDbObject($this);
    return $this;
  }

  public function setName(string $newName) : RecipeInterface {
    global $Controller;
    $this->name = $newName;
    $this->changes['recipe_name'] = $newName;
    $Controller->updateDbObject($this);
    return $this;
  }

  public function setPublic(bool $newValue) : RecipeInterface {
    global $Controller;
    $this->ispublic = $newValue;
    $this->published = ($newValue ? new DateTime() : null);
    $this->changes['recipe_public'] = intval($newValue);
    $this->changes['recipe_published'] = $this->ispublic ? Formatter::date_format($this->published, DTF_SQL) : null;
    $Controller->updateDbObject($this);
    return $this;
  }

  public function setSourceDescription(string $newDescription) : RecipeInterface {
    global $Controller;
    $this->sourcedesc = $newDescription;
    $this->changes['recipe_source_desc'] = $newDescription;
    $Controller->updateDbObject($this);
    return $this;
  }

  public function setSourceUrl(string $newUrl) : RecipeInterface {
    global $Controller;
    $this->sourceurl = $newUrl;
    $this->changes['recipe_source_url'] = $newUrl;
    $Controller->updateDbObject($this);
    return $this;
  }

}
