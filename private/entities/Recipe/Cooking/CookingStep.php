<?php

namespace Surcouf\Cookbook\Recipe\Cooking;

use Surcouf\Cookbook\DbObjectInterface;

if (!defined('CORE2'))
  exit;

class CookingStep implements CookingStepInterface, DbObjectInterface, \JsonSerializable {

  protected $step_id,
            $recipe_id,
            $step_no,
            $step_title,
            $step_data,
            $step_time_preparation,
            $step_time_cooking,
            $step_time_chill;
  private $changes = array();

  public function __construct(?array $record=null) {
    if (!is_null($record)) {
      $this->step_id = intval($record['step_id']);
      $this->recipe_id = intval($record['recipe_id']);
      $this->step_no = intval($record['step_no']);
      $this->step_title = $record['step_title'];
      $this->step_data = $record['step_data'];
      $this->step_time_preparation = (!is_null($record['step_time_preparation']) ? intval($record['step_time_preparation']) : -1);
      $this->step_time_cooking = (!is_null($record['step_time_cooking']) ? intval($record['step_time_cooking']) : -1);
      $this->step_time_chill = (!is_null($record['step_time_chill']) ? intval($record['step_time_chill']) : -1);
    } else {
      $this->step_id = intval($this->step_id);
      $this->recipe_id = intval($this->recipe_id);
      $this->step_no = intval($this->step_no);
      $this->step_time_preparation = (!is_null($this->step_time_preparation) ? intval($this->step_time_preparation) : -1);
      $this->step_time_cooking = (!is_null($this->step_time_cooking) ? intval($this->step_time_cooking) : -1);
      $this->step_time_chill = (!is_null($this->step_time_chill) ? intval($this->step_time_chill) : -1);
    }
  }

  public function getContent() : string {
    return $this->step_data;
  }

  public function getChillTime() : ?int {
    return $this->step_time_chill;
  }

  public function getCookingTime() : ?int {
    return $this->step_time_cooking;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getHtmlContent() : string {
    return str_replace("\r\n", '<br />', $this->step_data);
  }

  public function getId() : int {
    return $this->step_id;
  }

  public function getIndex() : int {
    return ($this->step_no - 1);
  }

  public function getPreparationTime() : ?int {
    return $this->step_time_preparation;
  }

  public function getRecipe() : RecipeInterface {
    global $Controller;
    return $Controller->OM()->Recipe($this->recipe_id);
  }

  public function getRecipeId() : int {
    return $this->recipe_id;
  }

  public function getStepNo() : int {
    return $this->step_no;
  }

  public function getTitle() : string {
    return $this->step_title;
  }

  public function jsonSerialize() {
    return [
      'index' => $this->step_no,
      'name' => $this->step_title,
      'userContent' => $this->step_data,
      'timeConsumed' => [
        'cooking' => ($this->step_time_cooking == -1 ? '' : $this->step_time_cooking),
        'preparing' => ($this->step_time_preparation == -1 ? '' : $this->step_time_preparation),
        'rest' => ($this->step_time_chill == -1 ? '' : $this->step_time_chill),
        'unit' => 'minutes',
      ]
    ];
  }

  public function setContent(string $newContent) : CookingStepInterface {
    global $Controller;
    if ($this->step_data != $newContent) {
      $this->step_data = $newContent;
      $this->changes['step_data'] = $newContent;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function setCookingTime(?int $newTime) : CookingStepInterface {
    global $Controller;
    if ($this->step_time_cooking != $newTime) {
      $this->step_time_cooking = $newTime;
      $this->changes['step_time_cooking'] = $newTime;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function setPreparationTime(?int $newTime) : CookingStepInterface {
    global $Controller;
    if ($this->step_time_preparation != $newTime) {
      $this->step_time_preparation = $newTime;
      $this->changes['step_time_preparation'] = $newTime;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function setRestTime(?int $newTime) : CookingStepInterface {
    global $Controller;
    if ($this->step_time_chill != $newTime) {
      $this->step_time_chill = $newTime;
      $this->changes['step_time_chill'] = $newTime;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function setStepNo(int $newNumber) : CookingStepInterface {
    global $Controller;
    if ($this->step_no != $newNumber) {
      $this->step_no = $newNumber;
      $this->changes['step_no'] = $newNumber;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function setTitle(string $newTitle) : CookingStepInterface {
    global $Controller;
    if ($this->step_title != $newTitle) {
      $this->step_title = $newTitle;
      $this->changes['step_title'] = $newTitle;
      $Controller->updateDbObject($this);
    }
    return $this;
  }

  public function update(array $payload) : bool {
    global $Controller;
    $this
      ->setContent($payload['description'])
      ->setTitle($payload['title'])
      ->setStepNo(intval($payload['no']))
      ->setCookingTime(intval($payload['timeCook']))
      ->setPreparationTime(intval($payload['timePrep']))
      ->setRestTime(intval($payload['timeRest']));
    return true;
  }

}
