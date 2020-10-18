<?php

namespace Surcouf\Cookbook\Recipe;

use \DateTime;
use Surcouf\Cookbook\ControllerInterface;
use Surcouf\Cookbook\EActivityType;
use Surcouf\Cookbook\DbObjectInterface;
use Surcouf\Cookbook\Database\EAggregationType;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;
use Surcouf\Cookbook\Database\ObjectTableMapper;
use Surcouf\Cookbook\Helper\ConverterHelper;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\Recipe\Cooking\CookingStep;
use Surcouf\Cookbook\Recipe\Cooking\CookingStepInterface;
use Surcouf\Cookbook\Recipe\Ingredients\Ingredient;
use Surcouf\Cookbook\Recipe\Ingredients\IngredientInterface;
use Surcouf\Cookbook\Recipe\Pictures\Picture;
use Surcouf\Cookbook\Recipe\Pictures\PictureInterface;
use Surcouf\Cookbook\Recipe\Social\Ratings\Rating;
use Surcouf\Cookbook\Recipe\Social\Ratings\RatingInterface;
use Surcouf\Cookbook\Recipe\Social\Tags\Tag;
use Surcouf\Cookbook\Recipe\Social\Tags\TagInterface;
use Surcouf\Cookbook\User\UserInterface;

if (!defined('CORE2'))
  exit;

class Recipe implements RecipeInterface, DbObjectInterface, \JsonSerializable {

  protected $recipe_id,
            $user_id,
            $recipe_public,
            $recipe_name,
            $recipe_description,
            $recipe_eater,
            $recipe_source_desc,
            $recipe_source_url,
            $recipe_created,
            $recipe_published;
  protected $timecooking = 0, $timepreparation = 0, $timerest = 0, $timetotal = 0;
  protected $countcooked = 0, $countrated = 0, $countviewed = 0, $countvoted = 0;
  protected $sumvoted = 0, $sumrated = 0;
  protected $ingredients = array();
  protected $pictures = array();
  protected $ratings = array();
  protected $steps = array();
  protected $tags = array();
  protected $tagvotes = array();
  private $changes = array();

  public function __construct(?array $record=null) {
    if (!is_null($record)) {
      $this->recipe_id = intval($record['recipe_id']);
      $this->user_id = (!is_null($record['user_id']) ? intval($record['user_id']) : null);
      $this->recipe_public = ConverterHelper::to_bool($record['recipe_public']);
      $this->recipe_name = $record['recipe_name'];
      $this->recipe_description = $record['recipe_description'];
      $this->recipe_eater = intval($record['recipe_eater']);
      $this->recipe_source_desc = $record['recipe_source_desc'];
      $this->recipe_source_url = $record['recipe_source_url'];
      $this->recipe_created = new DateTime($record['recipe_created']);
      $this->recipe_published = (!is_null($record['recipe_published']) ? new DateTime($record['recipe_published']) : null);
    } else {
      $this->user_id = (!is_null($this->user_id) ? intval($this->user_id) : null);
      $this->recipe_public = ConverterHelper::to_bool($this->recipe_public);
      $this->recipe_eater = intval($this->recipe_eater);
      $this->recipe_created = new DateTime($this->recipe_created);
      $this->recipe_published = (!is_null($this->recipe_published) ? new DateTime($this->recipe_published) : null);
    }
  }

  public function addCookingStep(CookingStepInterface &$step) : void {
    if ($step->getPreparationTime() > 0)
      $this->timepreparation += $step->getPreparationTime();
    if ($step->getCookingTime() > 0)
      $this->timecooking += $step->getCookingTime();
    if ($step->getChillTime() > 0)
      $this->timerest += $step->getChillTime();
    $this->timetotal = $this->timepreparation + $this->timecooking + $this->timerest;
    $this->steps[$step->getIndex()] = $step;
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
    return $this->recipe_created;
  }

  public function getCreationDateStr() : string {
    return Formatter::date_format($this->recipe_created);
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getDescription() : string {
    return $this->recipe_description;
  }

  public function getEaterCount() : int {
    return $this->recipe_eater;
  }

  public function getEaterCountStr() : string {
    return Formatter::int_format($this->recipe_eater);
  }

  public function getId() : int {
    return $this->recipe_id;
  }

  public function getIngredients() : array {
    return $this->ingredients;
  }

  public function getIngredientsCount() : int {
    return count($this->ingredients);
  }

  public function getName() : string {
    return $this->recipe_name;
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
    return $this->recipe_published;
  }

  public function getPublishedDateStr() : string {
    return Formatter::date_format($this->recipe_published);
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
    if (is_null($this->recipe_source_desc) || $this->recipe_source_desc == '') {
      if (is_null($this->recipe_source_url) || $this->recipe_source_url == '')
        return '';
      $pi = parse_url($this->recipe_source_url);
      return $pi['host'];
    }
    return $this->recipe_source_desc;
  }

  public function getSourceUrl() : string {
    return $this->recipe_source_url;
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
    return !is_null($this->user_id) ? $Controller->OM()->User($this->user_id) : null;
  }

  public function getUserId() : ?int {
    return $this->user_id;
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
    return $this->recipe_public;
  }

  public function jsonSerialize() {
    global $Controller;
    return [
      'id' => $this->recipe_id,
      'name' => $this->recipe_name,
      'created' => $this->recipe_created->format(DateTime::ISO8601),
      'description' => $this->recipe_description,
      'eaterCount' => $this->recipe_eater,
      'eaterCountCalc' => $this->recipe_eater,
      'ownerId' => (!is_null($this->user_id) ? $this->user_id : false),
      'ownerName' => (!is_null($this->user_id) ? $this->getUser()->getUsername() : ''),
      'published' => ($this->recipe_public ? $this->recipe_published->format(DateTime::ISO8601) : false),
      'source' => [
        'description' => $this->getSourceDescription(),
        'url' => $this->getSourceUrl(),
      ],
      'formatted' => [
        'created' => $this->recipe_created->format($Controller->Config()->DefaultDateFormatUi()),
        'published' => ($this->recipe_public ? $this->recipe_published->format($Controller->Config()->DefaultDateFormatUi()) : ''),
      ],
      'pictures' => array_values($this->pictures),
      'preparation' => [
        'ingredients' => array_values($this->ingredients),
        'steps' => $this->steps,
        'timeConsumed' => [
          'cooking' => $this->timecooking,
          'preparing' => $this->timepreparation,
          'rest' => $this->timerest,
          'total' => $this->timetotal,
          'unit' => 'minutes',
          'formatted' => [
            'cooking' => [
              'valueStr' => Formatter::min_format($this->timecooking),
              'timeStr' => $Controller->l('recipe_duration_cooking', $this->timecooking > 0 ? Formatter::min_format($this->timecooking) : $Controller->l('recipe_duration_notSet')),
              'timeStr2' => $Controller->l('recipe_duration_cooking', $this->timecooking > 0 ? Formatter::min_format($this->timecooking, false) : $Controller->l('recipe_duration_notSet')),
            ],
            'preparing' => [
              'valueStr' => Formatter::min_format($this->timepreparation),
              'timeStr' => $Controller->l('recipe_duration_preparation', $this->timepreparation > 0 ? Formatter::min_format($this->timepreparation) : $Controller->l('recipe_duration_notSet')),
              'timeStr2' => $Controller->l('recipe_duration_preparation', $this->timepreparation > 0 ? Formatter::min_format($this->timepreparation, false) : $Controller->l('recipe_duration_notSet')),
            ],
            'rest' => [
              'valueStr' => Formatter::min_format($this->timerest),
              'timeStr' => $Controller->l('recipe_duration_rest', $this->timerest > 0 ? Formatter::min_format($this->timerest) : $Controller->l('recipe_duration_notSet')),
              'timeStr2' => $Controller->l('recipe_duration_rest', $this->timerest > 0 ? Formatter::min_format($this->timerest, false) : $Controller->l('recipe_duration_notSet')),
            ],
            'total' => [
              'valueStr' => Formatter::min_format($this->timetotal),
            ]
          ]
        ]
      ],
      'socials' => [
        'cookedCounter' => $this->countcooked,
        'ratedCounter' => $this->countrated,
        'ratedSum' => $this->sumrated,
        'viewCounter' => $this->countviewed,
        'votedCounter' => $this->countvoted,
        'votedSum' => $this->sumvoted,
        'votedAvg1' => ($this->countvoted > 0 ? Formatter::float_format($this->sumvoted / $this->countvoted, 1) : null),
        'votedAvg0' => ($this->countvoted > 0 ? Formatter::float_format($this->sumvoted / $this->countvoted, 0) : null),
      ],
      'tags' => [
        'items' => $this->tags,
        'votes' => $this->tagvotes,
      ]
    ];
  }

  public function hasPictures() : bool {
    return count($this->pictures) > 0;
  }

  public function loadComplete() : void {
    global $Controller;
    $this->loadRecipeIngredients($Controller);
    $this->loadRecipePictures($Controller);
    $this->loadRecipeRatings($Controller);
    $this->loadRecipeSteps($Controller);
    $this->loadRecipeTags($Controller);
  }

  public function loadRecipeIngredients(ControllerInterface $Controller) : void {
    $mapper = ObjectTableMapper::getMapper(Ingredient::class);
    $query = new QueryBuilder(EQueryType::qtSELECT, $mapper->TableName(), DB_ANY);
    $query->where($mapper->TableName(), 'recipe_id', '=', $this->getId());
    $result = $Controller->select($query);
    if ($result) {
      while ($record = $result->fetch_object(Ingredient::class)) {
        $record = $Controller->OM()->Ingredient($record);
        $this->addIngredients($record);
      }
    }
  }

  public function loadRecipePictures(ControllerInterface $Controller) : void {
    $mapper = ObjectTableMapper::getMapper(Picture::class);
    $query = new QueryBuilder(EQueryType::qtSELECT, $mapper->TableName(), DB_ANY);
    $query->where($mapper->TableName(), 'recipe_id', '=', $this->getId());
    $result = $Controller->select($query);
    if ($result) {
      while ($record = $result->fetch_object(Picture::class)) {
        $record = $Controller->OM()->Picture($record);
        $this->addPicture($record);
      }
    }
  }

  public function loadRecipeRatings(ControllerInterface $Controller) : void {
    $mapper = ObjectTableMapper::getMapper(Rating::class);
    $query = new QueryBuilder(EQueryType::qtSELECT, $mapper->TableName(), DB_ANY);
    $query->where($mapper->TableName(), 'recipe_id', '=', $this->getId());
    $result = $Controller->select($query);
    if ($result) {
      while ($record = $result->fetch_object(Rating::class)) {
        $record = $Controller->OM()->Rating($record);
        $this->addRating($record);
      }
    }
  }

  public function loadRecipeSteps(ControllerInterface $Controller) : void {
    $mapper = ObjectTableMapper::getMapper(CookingStep::class);
    $query = new QueryBuilder(EQueryType::qtSELECT, $mapper->TableName(), DB_ANY);
    $query->where($mapper->TableName(), 'recipe_id', '=', $this->getId());
    $result = $Controller->select($query);
    if ($result) {
      while ($record = $result->fetch_object(CookingStep::class)) {
        $record = $Controller->OM()->CookingStep($record);
        $this->addCookingStep($record);
      }
    }
  }

  public function loadRecipeTags(ControllerInterface $Controller) : void {
    $mapper = ObjectTableMapper::getMapper(Tag::class);
    $query = new QueryBuilder(EQueryType::qtSELECT, 'recipe_tags');
    $query->select($mapper->TableName(), DB_ANY)
          ->select($mapper->TableName(), [[$mapper->IdColumn(), EAggregationType::atCOUNT, 'count']])
          ->join($mapper->TableName(),
            [$mapper->TableName(), $mapper->IdColumn(), '=', 'recipe_tags', 'tag_id'],
            ['AND', 'recipe_tags', 'recipe_id', '=', $this->getId()])
          ->groupBy($mapper->TableName(), [$mapper->IdColumn(), 'tag_name'])
          ->orderBy2(null, 'count', 'DESC');
    $result = $Controller->select($query);
    if ($result) {
      while ($record = $result->fetch_array()) {
        $tag = $Controller->OM()->Tag($record);
        $this->addTag($tag, intval($record['count']));
      }
    }
  }

  public function setDescription(string $newDescription) : RecipeInterface {
    global $Controller;
    if ($this->recipe_description != $newDescription) {
      $this->recipe_description = $newDescription;
      $this->changes['recipe_description'] = $newDescription;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function setEaterCount(int $newCount) : RecipeInterface {
    global $Controller;
    if ($this->recipe_eater != $newCount) {
      $this->recipe_eater = $newCount;
      $this->changes['recipe_eater'] = $newCount;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function setName(string $newName) : RecipeInterface {
    global $Controller;
    if ($this->recipe_name != $newName) {
      $this->recipe_name = $newName;
      $this->changes['recipe_name'] = $newName;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function setPublic(bool $newValue) : RecipeInterface {
    global $Controller;
    $this->recipe_public = $newValue;
    $this->recipe_published = ($newValue ? new DateTime() : null);
    $this->changes['recipe_public'] = intval($newValue);
    $this->changes['recipe_published'] = $this->recipe_public ? Formatter::date_format($this->recipe_published, DTF_SQL) : null;
    $Controller->updateDbObject($this);
    $Controller->addActivity(
      $newValue == true ? EActivityType::RecipePublished : EActivityType::RecipeRemoved, [], $this);
    return $this;
  }

  public function setSourceDescription(string $newDescription) : RecipeInterface {
    global $Controller;
    if ($this->recipe_source_desc != $newDescription) {
      $this->recipe_source_desc = $newDescription;
      $this->changes['recipe_source_desc'] = $newDescription;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function setSourceUrl(string $newUrl) : RecipeInterface {
    global $Controller;
    if ($this->recipe_source_url != $newUrl) {
      $this->recipe_source_url = $newUrl;
      $this->changes['recipe_source_url'] = $newUrl;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function update(array &$response, array $payload) : bool {
    global $Controller;
    $this
      ->setDescription($payload['recipe-description'])
      ->setEaterCount(intval($payload['recipe-eater']))
      ->setName($payload['recipe-name'])
      ->setSourceDescription($payload['recipe-source'])
      ->setSourceUrl($payload['recipe-sourceurl']);
    
    $response = $Controller->Config()->getResponseArray(91);
    return false;
  }

}
