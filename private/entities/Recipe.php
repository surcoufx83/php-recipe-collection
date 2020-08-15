<?php

namespace Surcouf\PhpArchive;

use \DateTime;
use Surcouf\PhpArchive\Database\EAggregationType;
use Surcouf\PhpArchive\Database\EQueryType;
use Surcouf\PhpArchive\Database\QueryBuilder;
use Surcouf\PhpArchive\Helper\ConverterHelper;
use Surcouf\PhpArchive\Helper\Formatter;

if (!defined('CORE2'))
  exit;

class Recipe implements IRecipe, IDbObject {

  private $id, $userid, $ispublic, $name, $description, $eater, $sourcedesc, $sourceurl, $created, $published;
  private $countcooked = 0, $countvoted = 0, $countrated = 0;
  private $sumvoted = 0, $sumrated = 0;
  private $ingredients = array();
  private $pictures = array();
  private $ratings = array();
  private $steps = array();
  private $tags = array();
  private $tagvotes = array();
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['recipe_id']);
    $this->userid = intval($dr['user_id']);
    $this->ispublic = ConverterHelper::to_bool($dr['recipe_public']);
    $this->name = $dr['recipe_name'];
    $this->description = $dr['recipe_description'];
    $this->eater = intval($dr['recipe_eater']);
    $this->sourcedesc = $dr['recipe_source_desc'];
    $this->sourceurl = $dr['recipe_source_url'];
    $this->created = new DateTime($dr['recipe_created']);
    $this->published = (!is_null($dr['recipe_published']) ? new DateTime($dr['recipe_published']) : null);
  }

  public function addIngredients(IIngredient &$ingredient, ?IUnit &$unit, array $record) : void {
    $this->ingredients[] = [
      'Ingredient' => $ingredient,
      'Unit' => $unit,
      'Amount' => floatval($record['entry_amount']),
      'TitleDe' => $record['entry_title_de'],
      'TitleEn' => $record['entry_title_en'],
      'DescriptionDe' => $record['entry_description_de'],
      'DescriptionEn' => $record['entry_description_en'],
    ];
  }

  public function addPicture(IPicture &$picture) : void {
    $this->pictures[$picture->getIndex()] = $picture;
  }

  public function addRating(IRating &$rating) : void {
    $this->ratings[] = $rating;
    if ($rating->hasCooked())
      $this->countcooked++;
    if ($rating->hasVoted()) {
      $this->countvoted++;
      $this->sumvoted += $rating->getVoting();
    }
    if ($rating->hasRated()) {
      $this->countrated++;
      $this->sumrated += $rating->getRating();
    }
  }

  public function addStep(ICookingStep &$step) : void {
    $this->steps[$step->getIndex()] = $step;
  }

  public function addTag(ITag &$tag, int $votes) : void {
    $this->tags[$tag->getId()] = $tag;
    $this->tagvotes[$tag->getId()] = $votes;
  }

  public function getCookedCount() : int {
    return $this->countcooked;
  }

  public function getCookedCountStr() : string {
    return Formatter::int_format($this->countcooked);
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

  public function getName() : string {
    return $this->name;
  }

  public function getPictures() : array {
    return $this->pictures;
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
    if ($this->countrated == 0)
      return '';
    return Formatter::float_format($this->getRating(), 1);
  }

  public function getRatings() : array {
    return $this->ratings;
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

  public function getTags() : array {
    return $this->tags;
  }

  public function getTagVotes() : array {
    return $this->tagvotes;
  }

  public function getUser() : ?User {
    global $Controller;
    return $Controller->getUser($this->userid);
  }

  public function getUserId() : ?int {
    return $this->userid;
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

  public function loadComplete() : void {
    global $Controller;
    $Controller->loadRecipeIngredients($this);
    $Controller->loadRecipePictures($this);
    $Controller->loadRecipeRatings($this);
    $Controller->loadRecipeSteps($this);
    $Controller->loadRecipeTags($this);
  }

}
