<?php

$Controller->get(array(
  'pattern' => '/dropzone',
  'fn' => 'ui_dropzone'
));

function ui_dropzone() {
  global $OUT, $twig;
  $OUT['Page']['Current'] = 'dropzone:main';
  $OUT['Page']['Heading1'] = 'Dropzone todo';
} // ui_dropzone()
