<?php

use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;

$Controller->get(array(
  'pattern' => '/random',
  'fn' => 'ui_random'
));

function ui_random() {
  global $Controller, $OUT, $twig;

  $query = new QueryBuilder(EQueryType::qtSELECT, 'recipes');
  $query->select('recipes', ['recipe_id', 'recipe_name'])
        ->where('recipes', 'recipe_public', '=', 1)
        ->orderRandom()
        ->limit(1);
  $result = $Controller->select($query);
  if (!$result || $result->num_rows == 0)
    $Controller->Dispatcher()->forward($Controller->getLink('private:home'));

  $record = $result->fetch_assoc();
  $Controller->Dispatcher()->forward($Controller->getLink('recipe:show', $record['recipe_id'], $record['recipe_name']));

} // ui_random()
