<?php

namespace Surcouf\PhpArchive;

if (!defined('CORE2'))
  exit;

class CookingStep implements ICookingStep, IDbObject {

  private $id, $recipeid, $stepno, $title, $content;
  private $timeprep, $timecook, $timechill;
  private $changes = array();

  public function __construct($dr) {
    $this->id = intval($dr['step_id']);
    $this->recipeid = intval($dr['recipe_id']);
    $this->stepno = intval($dr['step_no']);
    $this->title = $dr['step_title'];
    $this->content = $dr['step_data'];
    $this->timeprep = (!is_null($dr['step_time_preparation']) ? intval($dr['step_time_preparation']) : -1);
    $this->timecook = (!is_null($dr['step_time_cooking']) ? intval($dr['step_time_cooking']) : -1);
    $this->timechill = (!is_null($dr['step_time_chill']) ? intval($dr['step_time_chill']) : -1);
  }

  public function getContent() : string {
    return $this->content;
  }

  public function getChillTime() : int {
    return $this->timeprep;
  }

  public function getCookingTime() : int {
    return $this->timeprep;
  }

  public function getDbChanges() : array {
    return $this->changes;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getIndex() : int {
    return ($this->stepno - 1);
  }

  public function getPreparationTime() : int {
    return $this->timeprep;
  }

  public function getRecipe() : ?Recipe {
    global $Controller;
    return $Controller->getRecipe($this->recipeid);
  }

  public function getRecipeId() : ?int {
    return $this->recipeid;
  }

  public function getStepNo() : int {
    return $this->stepno;
  }

  public function getTitle() : string {
    return $this->title;
  }

}
