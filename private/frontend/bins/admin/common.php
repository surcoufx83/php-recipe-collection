<?php

use Surcouf\Cookbook\BlankUser;
use Surcouf\Cookbook\Response\EOutputMode;

$Controller->post(array(
  'pattern' => '/admin/test/entity',
  'fn' => 'ui_admin_test_entity',
  'outputMode' => EOutputMode::JSON
));

function ui_admin_test_entity() {
  global $Controller;

  if (!$Controller->isAuthenticated() || !$Controller->User()->isAdmin())
    return $Controller->Config()->getResponseArray(120);

  $payload = $Controller->Dispatcher()->getPayload();
  $messages = [];
  $success = true;

  foreach ($payload as $key => $value) {
    switch($key) {
      case 'user-email':
        if ($Controller->selectCountSimple('users', 'user_email', $value) != 0) {
          $success = false;
          $messages[$key] = $Controller->l('page_admin_newUser_chapter1_email_serverFeedback', $value);
        }
        break;
      case 'user-name':
        if ($Controller->selectCountSimple('users', 'user_name', $value) != 0) {
          $success = false;
          $messages[$key] = $Controller->l('page_admin_newUser_chapter1_name_serverFeedback', $value);
        }
        break;
      default:
        $success = false;
        $messages[$key] = $Controller->l('page_admin_newUser_chapter1_serverFeedback', $key);
    }
  }

  if ($success)
    return $Controller->Config()->getResponseArray(90);

  $response = $Controller->Config()->getResponseArray(91);
  $response['fields'] = $messages;
  return $response;

} // ui_admin_test_entity()
