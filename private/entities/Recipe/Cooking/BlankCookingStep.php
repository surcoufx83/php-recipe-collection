<?php

namespace Surcouf\Cookbook\Recipe\Cooking;

if (!defined('CORE2'))
  exit;

class BlankCookingStep extends CookingStep {

  public function __construct(int $step, string $title, string $content, string $preptime, string $chilltime, string $cookingtime) {
    $this->step_no = $step;
    $this->step_title = $title;
    $this->step_data = $content;
    $this->step_time_preparation = ($preptime != '' ? intval($preptime) : -1);
    $this->step_time_cooking = ($cookingtime != '' ? intval($cookingtime) : -1);
    $this->step_time_chill = ($chilltime != '' ? intval($chilltime) : -1);
  }

}
