<?php

namespace Surcouf\Cookbook;

use \DateInterval;
use Surcouf\Cookbook\Config\DatabaseManagerInterface;
use Symfony\Component\Yaml\Yaml;

if (!defined('CORE2'))
  exit;

final class Config implements ConfigInterface {

  public const CTYPE_DBCREDENTIALS = 1;
  public const CTYPE_MAILCREDENTIALS = 2;
  public const CTYPE_OAUTHCREDENTIALS = 3;

  private $config, $icons, $responses;

  public function __construct() {
    if (!\file_exists(DIR_CONFIG.DS.'cbconfig.yml'))
      throw new \Exception("cbconfig.yml not found in folder config. Please check cbconfig.yml.templat for more information.", 1);
    if (!\file_exists(DIR_CONFIG.DS.'cbicons.yml'))
      throw new \Exception("cbicons.yml not found in folder config.", 1);

    $this->config = Yaml::parse(file_get_contents(DIR_CONFIG.DS.'cbconfig.yml'));
    $this->config['System']['MaintenanceMode'] = file_exists(ROOT.DS.'.maintenance.tmp');
    $this->icons = Yaml::parse(file_get_contents(DIR_CONFIG.DS.'cbicons.yml'));
    define('MAINTENANCE', $this->config['System']['MaintenanceMode']);
  }

  public function initController() : void {
    global $Controller;
    $this->responses = [
        1 => ['code' =>   1, 'message' => '', 'success' => true],
        2 => ['code' =>   2, 'message' => $Controller->l('response_noChanges'), 'success' => true],
        3 => ['code' =>   3, 'message' => $Controller->l('response_noResults'), 'success' => true],
        4 => ['code' =>   4, 'message' => '', 'success' => true, 'forward' => []],
       10 => ['code' =>  10, 'message' => $Controller->l('response_undefinedException'), 'success' => false],
       11 => ['code' =>  11, 'message' => $Controller->l('response_badRequestException'), 'success' => false],
       12 => ['code' =>  12, 'message' => $Controller->l('response_pageMovedException'), 'success' => false],
       30 => ['code' =>  30, 'message' => $Controller->l('response_loginFailed'), 'success' => false],
       31 => ['code' =>  31, 'message' => $Controller->l('response_loginSuccessfull'), 'success' => true],
       70 => ['code' =>  70, 'message' => $Controller->l('response_parameterException'), 'success' => false],
       71 => ['code' =>  71, 'message' => $Controller->l('response_functionNotFoundException'), 'success' => false],
       80 => ['code' =>  80, 'message' => $Controller->l('response_badArgumentsException'), 'success' => false],
       90 => ['code' =>  90, 'message' => $Controller->l('response_validationSucceeded'), 'success' => true],
       91 => ['code' =>  91, 'message' => $Controller->l('response_validationFailed'), 'success' => false],
       92 => ['code' =>  92, 'message' => $Controller->l('response_notAllowedException'), 'success' => false],
      100 => ['code' => 100, 'message' => $Controller->l('response_maintenanceException'), 'success' => false],
      110 => ['code' => 110, 'message' => $Controller->l('response_notAuthenticatedException'), 'success' => false],
      111 => ['code' => 111, 'message' => $Controller->l('response_noApiKeyException'), 'success' => false],
      120 => ['code' => 120, 'message' => $Controller->l('response_insufficientPermissionException'), 'success' => false],
      201 => ['code' => 201, 'message' => $Controller->l('response_dbStmtException'), 'success' => false],
      202 => ['code' => 202, 'message' => $Controller->l('response_dbInsertException'), 'success' => false],
      203 => ['code' => 203, 'message' => $Controller->l('response_dbUpdateException'), 'success' => false],
      204 => ['code' => 204, 'message' => $Controller->l('response_dbSelectException'), 'success' => false],
      210 => ['code' => 210, 'message' => $Controller->l('response_sendMailFailed'), 'success' => false],
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

  public function getIcon(string $key) : ?array {
    if (!array_key_exists($key, $this->icons))
      return null;
    return $this->icons[$key];
  }

  public function getIconKeys() : array {
    return array_keys($this->icons);
  }

  public function getResponseArray(int $responseCode) : array {
    return array_key_exists($responseCode, $this->responses) ? $this->responses[$responseCode] : $this->responses[10];
  }

}
