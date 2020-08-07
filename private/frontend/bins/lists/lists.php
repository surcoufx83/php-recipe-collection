<?php

$Controller->get(array(
  'pattern' => '/lists',
  'fn' => 'ui_lists'
));

function ui_lists() {
  global $OUT, $twig;
  $OUT['Page']['Current'] = 'private:lists';
  $OUT['Page']['Heading1'] = lang('lists_all');
} // ui_lists()
