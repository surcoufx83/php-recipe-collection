<?php

use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;

$Controller->get(array(
  'pattern' => '/books',
  'fn' => 'ui_books'
));

$Controller->get(array(
  'pattern' => '/myrecipes',
  'fn' => 'ui_myrecipes'
));

function ui_books() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => lang('page_books_allbooks_title'),
    'url' => $Controller->getLink('private:books'),
  );

  $OUT['Page']['Current'] = 'private:books';
  $OUT['Page']['Heading1'] = lang('page_books_allbooks_title');
} // ui_books()

function ui_myrecipes() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => lang('page_books_allbooks_title'),
    'url' => $Controller->getLink('private:books'),
  );

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => lang('page_books_allbooks_recipes'),
    'url' => $Controller->getLink('private:recipes'),
  );

  $recipes = [];
  $query = new QueryBuilder(EQueryType::qtSELECT, 'recipes', DB_ANY);
  $query->where('recipes', 'user_id', '=', $Controller->User()->getId());
  $result = $Controller->select($query);
  while ($record = $result->fetch_assoc()) {
    $recipe = $Controller->getRecipe($record);
    $Controller->loadRecipePictures($recipe);
    $Controller->loadRecipeRatings($recipe);
    $Controller->loadRecipeTags($recipe);
    $recipes[] = $recipe;
  }

  $OUT['Recipes'] = $recipes;
  $OUT['Page']['Current'] = 'private:books';
  $OUT['Page']['CurrentSub'] = 'private:myrecipes';
  $OUT['Page']['Heading1'] = lang('page_books_allbooks_recipes');
  $OUT['Content'] = $twig->render('views/books/myrecipes.html.twig', $OUT);
} // ui_myrecipes()
