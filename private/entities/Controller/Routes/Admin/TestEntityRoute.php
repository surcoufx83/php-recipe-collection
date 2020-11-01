<?php

namespace Surcouf\Cookbook\Controller\Routes\Admin;

use Surcouf\Cookbook\Controller\Route;
use Surcouf\Cookbook\Controller\RouteInterface;

if (!defined('CORE2'))
  exit;

class TestEntityRoute extends Route implements RouteInterface {

  static function createOutput(array &$response) : bool {
    global $Controller;

      $payload = $Controller->Dispatcher()->getPayload();
      $messages = [];
      $success = true;

      foreach ($payload as $key => $value) {
        switch($key) {
          case 'user-email':
            if ($Controller->selectCountSimple('users', 'user_email', $value) != 0) {
              $success = false;
              // todo
              //$messages[$key] = $Controller->l('page_admin_newUser_chapter1_email_serverFeedback', $value);
            }
            break;
          case 'user-name':
            if ($Controller->selectCountSimple('users', 'user_name', $value) != 0) {
              $success = false;
              // todo
              //$messages[$key] = $Controller->l('page_admin_newUser_chapter1_name_serverFeedback', $value);
            }
            break;
          default:
            $success = false;
            // todo
            //$messages[$key] = $Controller->l('page_admin_newUser_chapter1_serverFeedback', $key);
        }
      }

      if ($success) {
        $response = $Controller->Config()->getResponseArray(90);
        return true;
      }

      $response = $Controller->Config()->getResponseArray(91);
      $response['fields'] = $messages;
      return false;

  }

}
