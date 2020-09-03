<?php

$Controller->get(array(
  'pattern' => '/search',
  'fn' => 'ui_search'
));

function ui_search() {
  global $OUT, $twig;
  $OUT['Page']['Current'] = 'private:search';
  $OUT['Page']['Heading1'] = 'Searcg todo';
} // ui_search()
