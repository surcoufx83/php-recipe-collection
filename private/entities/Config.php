<?php

namespace Surcouf\Cookbook;

use \DateInterval;
use Surcouf\Cookbook\Config\IconConfig;
use Surcouf\Cookbook\Config\IconConfigInterface;

if (!defined('CORE2'))
  exit;

final class Config implements ConfigInterface {

  private $config, $icocfg, $responses;

  public function __construct() {
    global $Controller;
    $this->config = [
      'AllowRegistration'           => false,
      'ChecksumProvider'            => 'adler32',
      'ConsentCookieName'           => 'kbconsenttoken',
      'CronjobsEnabled'             => true,
      'DbDateFormat'                => 'Y-m-d',
      'DefaultDateFormat'           => 'd.m.Y',
      'DefaultDateFormatUi'         => 'd. F Y',
      'DefaultDateTimeFormat'       => 'd.m.Y H:i:s',
      'DefaultLongDateTimeFormat'   => 'l, d. F Y H:i:s',
      'DefaultDecimalsCount'        => 2,
      'DefaultDecimalsSeparator'    => ',',
      'DefaultListEntries'          => 10,
      'DefaultTimeFormat'           => 'H:i:s',
      'DefaultThousandsSeparator'   => '.',
      'HashProvider'                => 'crc32b',
      'LogCleanupTime'              => new DateInterval('P1M'),
      'LongTimeWarning'             => 180,
      'MaintenanceMode'             => file_exists(ROOT.DS.'.maintenance.tmp'),
      'OAuth2Enabled'               => file_exists(DIR_BACKEND.DS.'conf.oauth2.php'),
      'PageForceHttps'              => false,
      'PageHeader'                  => 'Kochbuch',
      'PageTitle'                   => 'Kochbuch',
      'PageUrls'                    => [
                                        'kochbuch.mogul.network',
                                        'localhost',
                                        '127.0.0.1',
                                       ],
      'PasswordCookieName'          => 'kbpasstoken',
      'PublicContact'               => 'Elias und Stefan',
      'PublicSignature'             => 'Kochbuch-Team',
      'PublicUrl'                   => 'kochbuch.mogul.network',
      'RecipeRatingClearance'       => new DateInterval('P30D'),
      'RecipeVisitedClearance'      => new DateInterval('P3D'),
      'SessionCookieName'           => 'kbsessiontoken',
      'SessionCleanupTime'          => new DateInterval('PT15M'),
      'SessionLongExpirationTime'   => new DateInterval('P1Y'),
      'SessionShortExpirationTime'  => new DateInterval('PT1H'),
      'UserCookieName'              => 'kbusertoken',
    ];
    $this->icocfg = new IconConfig();
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
    return array_key_exists($methodName, $this->config) ? $this->config[$methodName] : null;
  }

  public function __get(string $propertyName) {
    return array_key_exists($propertyName, $this->config) ? $this->config[$propertyName] : null;
  }

  public function getResponseArray(int $responseCode) : array {
    return array_key_exists($responseCode, $this->responses) ? $this->responses[$responseCode] : $this->responses[10];
  }

  public function Icons() : IconConfigInterface {
    return $this->icocfg;
  }

}
