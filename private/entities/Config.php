<?php

namespace Surcouf\Cookbook;

use \DateInterval;
use Surcouf\Cookbook\Config\DatabaseManagerInterface;
use Surcouf\Cookbook\Config\IconConfig;
use Surcouf\Cookbook\Config\IconConfigInterface;

if (!defined('CORE2'))
  exit;

final class Config implements ConfigInterface {

  public const CTYPE_DBCREDENTIALS = 1;
  public const CTYPE_MAILCREDENTIALS = 2;
  public const CTYPE_OAUTHCREDENTIALS = 3;

  private $config, $icocfg, $responses;

  public function __construct(array $configuration) {
    spddg(__FILE__, '', __CLASS__, __METHOD__);
    $this->config = $configuration;
    $this->config['System']['MaintenanceMode'] = file_exists(ROOT.DS.'.maintenance.tmp');
    define('MAINTENANCE', $this->config['System']['MaintenanceMode']);
  }

  public function initController() : void {
    spddg(__FILE__, '', __CLASS__, __METHOD__);
    global $Controller;
    $this->icocfg = new IconConfig();
    $this->responses = [
        1 => ['code' =>   1, 'message' => '', 'success' => true],
        2 => ['code' =>   2, 'message' => 'response_noChanges', 'success' => true],
        3 => ['code' =>   3, 'message' => 'response_noResults', 'success' => true],
        4 => ['code' =>   4, 'message' => '', 'success' => true, 'forward' => []],
       10 => ['code' =>  10, 'message' => 'response_undefinedException', 'success' => false],
       11 => ['code' =>  11, 'message' => 'response_badRequestException', 'success' => false],
       12 => ['code' =>  12, 'message' => 'response_pageMovedException', 'success' => false],
       30 => ['code' =>  30, 'message' => 'response_loginFailed', 'success' => false],
       31 => ['code' =>  31, 'message' => 'response_loginSuccessfull', 'success' => true],
       70 => ['code' =>  70, 'message' => 'response_parameterException', 'success' => false],
       71 => ['code' =>  71, 'message' => 'response_functionNotFoundException', 'success' => false],
       80 => ['code' =>  80, 'message' => 'response_badArgumentsException', 'success' => false],
       90 => ['code' =>  90, 'message' => 'response_validationSucceeded', 'success' => true],
       91 => ['code' =>  91, 'message' => 'response_validationFailed', 'success' => false],
       92 => ['code' =>  92, 'message' => 'response_notAllowedException', 'success' => false],
      100 => ['code' => 100, 'message' => 'response_maintenanceException', 'success' => false],
      110 => ['code' => 110, 'message' => 'response_notAuthenticatedException', 'success' => false],
      111 => ['code' => 111, 'message' => 'response_noApiKeyException', 'success' => false],
      120 => ['code' => 120, 'message' => 'response_insufficientPermissionException', 'success' => false],
      201 => ['code' => 201, 'message' => 'response_dbStmtException', 'success' => false],
      202 => ['code' => 202, 'message' => 'response_dbInsertException', 'success' => false],
      203 => ['code' => 203, 'message' => 'response_dbUpdateException', 'success' => false],
      204 => ['code' => 204, 'message' => 'response_dbSelectException', 'success' => false],
      210 => ['code' => 210, 'message' => 'response_sendMailFailed', 'success' => false],
    ];
  }

  public function __call(string $methodName, array $params) {
    if (!array_key_exists($methodName, $this->config) || count($params) == 0)
      return null;
    $obj = $this->config[$methodName];
    for ($i=0; $i<count($params); $i++) {
      if (!array_key_exists($params[$i], $obj) || $params[$i] == 'Credentials')
        return null;
      $obj = $obj[$params[$i]];
    }
    return $obj;
  }

  public function getCredentials(object $obj, int $type) : bool {
    if ($type == self::CTYPE_DBCREDENTIALS && is_a($obj, DatabaseManagerInterface::class)) {
      $obj->setDatabaseHost($this->config['System']['Database']['Host'])
          ->setDatabaseUser($this->config['System']['Database']['Credentials']['Name'])
          ->setDatabasePassword($this->config['System']['Database']['Credentials']['Password'])
          ->setDatabaseDbName($this->config['System']['Database']['Database']);
      return true;
    }
    return false;
  }

  public function getResponseArray(int $responseCode) : array {
    global $Controller;
    $response = array_key_exists($responseCode, $this->responses) ? $this->responses[$responseCode] : $this->responses[10];
    if ($response['message'] != '')
      $response['message'] = $Controller->l($response['message']);
    return $response;
  }

  public function Icons() : IconConfigInterface {
    return $this->icocfg;
  }

}
