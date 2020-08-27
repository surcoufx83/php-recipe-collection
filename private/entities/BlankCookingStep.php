<?php

namespace Surcouf\Cookbook;

if (!defined('CORE2'))
  exit;

class BlankCookingStep extends CookingStep {

  private $id, $recipeid, $stepno, $title, $content;
  private $timeprep, $timecook, $timechill;

  public function __construct(int $step, string $title, string $content, string $preptime, string $chilltime, string $cookingtime) {
    $this->stepno = $step;
    $this->title = $title;
    $this->content = $content;
    $this->timeprep = ($preptime != '' ? intval($preptime) : -1);
    $this->timecook = ($chilltime != '' ? intval($chilltime) : -1);
    $this->timechill = ($cookingtime != '' ? intval($cookingtime) : -1);
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
