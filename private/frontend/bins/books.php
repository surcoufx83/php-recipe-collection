<?php

use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;

$Controller->get(array(
  'pattern' => '/books',
  'fn' => 'ui_books'
));

function ui_books() {
  global $Controller, $OUT, $twig;

  $OUT['Page']['Breadcrumbs'][] = array(
    'text' => lang('page_books_allbooks_title'),
    'url' => $Controller->getLink('private:books'),
  );

  $OUT['Page']['Current'] = 'private:books';
  $OUT['Page']['Heading1'] = lang('page_books_allbooks_title');
  $OUT['Content'] = $twig->render('views/dummy.html.twig', $OUT);
} // ui_books()
