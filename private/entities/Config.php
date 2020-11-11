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
    if (!defined('MAINTENANCE'))
      define('MAINTENANCE', $this->config['System']['MaintenanceMode']);
  }

  public function initController() : void {
    global $Controller;
    $this->responses = [
        1 => ['code' =>   1, 'message' => '', 'i18nmessage' => '', 'success' => true],
        2 => ['code' =>   2, 'message' => '', 'i18nmessage' => 'responseMessages.noChanges', 'success' => true],
        3 => ['code' =>   3, 'message' => '', 'i18nmessage' => 'responseMessages.noResults', 'success' => true],
        4 => ['code' =>   4, 'message' => '', 'i18nmessage' => '', 'success' => true, 'forward' => []],
       10 => ['code' =>  10, 'message' => '', 'i18nmessage' => 'responseMessages.undefinedException', 'success' => false],
       11 => ['code' =>  11, 'message' => '', 'i18nmessage' => 'responseMessages.badRequestException', 'success' => false],
       12 => ['code' =>  12, 'message' => '', 'i18nmessage' => 'responseMessages.pageMovedException', 'success' => false],
       30 => ['code' =>  30, 'message' => '', 'i18nmessage' => 'responseMessages.loginFailed', 'success' => false],
       31 => ['code' =>  31, 'message' => '', 'i18nmessage' => 'responseMessages.loginSuccessfull', 'success' => true],
       32 => ['code' =>  32, 'message' => '', 'i18nmessage' => 'responseMessages.oauthFailed', 'success' => false],
       70 => ['code' =>  70, 'message' => '', 'i18nmessage' => 'responseMessages.parameterException', 'success' => false],
       71 => ['code' =>  71, 'message' => '', 'i18nmessage' => 'responseMessages.functionNotFoundException', 'success' => false],
       80 => ['code' =>  80, 'message' => '', 'i18nmessage' => 'responseMessages.badArgumentsException', 'success' => false],
       81 => ['code' =>  81, 'message' => '', 'i18nmessage' => 'responseMessages.missingArgumentsException', 'success' => false],
       90 => ['code' =>  90, 'message' => '', 'i18nmessage' => 'responseMessages.validationSucceeded', 'success' => true],
       91 => ['code' =>  91, 'message' => '', 'i18nmessage' => 'responseMessages.validationFailed', 'success' => false],
       92 => ['code' =>  92, 'message' => '', 'i18nmessage' => 'responseMessages.notAllowedException', 'success' => false],
      100 => ['code' => 100, 'message' => '', 'i18nmessage' => 'responseMessages.maintenanceException', 'success' => false],
      110 => ['code' => 110, 'message' => '', 'i18nmessage' => 'responseMessages.notAuthenticatedException', 'success' => false],
      111 => ['code' => 111, 'message' => '', 'i18nmessage' => 'responseMessages.noApiKeyException', 'success' => false],
      120 => ['code' => 120, 'message' => '', 'i18nmessage' => 'responseMessages.insufficientPermissionException', 'success' => false],
      201 => ['code' => 201, 'message' => '', 'i18nmessage' => 'responseMessages.dbStmtException', 'success' => false],
      202 => ['code' => 202, 'message' => '', 'i18nmessage' => 'responseMessages.dbInsertException', 'success' => false],
      203 => ['code' => 203, 'message' => '', 'i18nmessage' => 'responseMessages.dbUpdateException', 'success' => false],
      204 => ['code' => 204, 'message' => '', 'i18nmessage' => 'responseMessages.dbSelectException', 'success' => false],
      210 => ['code' => 210, 'message' => '', 'i18nmessage' => 'responseMessages.sendMailFailed', 'success' => false],
      301 => ['code' => 301, 'message' => '', 'i18nmessage' => 'responseMessages.badImageData', 'success' => false],
      302 => ['code' => 302, 'message' => '', 'i18nmessage' => 'responseMessages.mimetypeNotSupported', 'success' => false],
      303 => ['code' => 303, 'message' => '', 'i18nmessage' => 'responseMessages.saveFileFailed', 'success' => false],
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
