<?php

namespace Surcouf\Cookbook\Recipe\Cooking;

if (!defined('CORE2'))
  exit;

class BlankCookingStep extends CookingStep {

  public function __construct(int $step, string $title, string $content, string $preptime, string $chilltime, string $cookingtime) {
    $this->stepno = $step;
    $this->title = $title;
    $this->content = $content;
    $this->timeprep = ($preptime != '' ? intval($preptime) : -1);
    $this->timecook = ($chilltime != '' ? intval($chilltime) : -1);
    $this->timechill = ($cookingtime != '' ? intval($cookingtime) : -1);
  }

}
